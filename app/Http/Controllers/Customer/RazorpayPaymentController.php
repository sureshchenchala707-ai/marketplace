<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\PaymentStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use App\Services\OrderFinalizationService;

class RazorpayPaymentController extends Controller
{
    /**
     * Razorpay API instance.
     */
    private function api(): Api
    {
        return new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    /**
     * Create or reopen Razorpay checkout.
     */
    public function initiate(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->refresh();

        if ($order->payment_status === 'paid') {
            return redirect()
                ->route('account.orders.show', $order)
                ->with('success', 'This order is already paid.');
        }

        if (
            $order->status !== 'pending' ||
            $order->stock_released_at ||
            (
                $order->payment_expires_at &&
                $order->payment_expires_at->isPast()
            )
        ) {
            return redirect()
                ->route('account.orders.show', $order)
                ->with(
                    'error',
                    'Payment reservation expired. Please place a new order.'
                );
        }

        $payment = $order->payments()
            ->where('gateway', 'razorpay')
            ->latest('id')
            ->first();

        if (
            !$payment ||
            !in_array(
                $payment->status,
                ['created', 'pending', 'failed'],
                true
            )
        ) {
            return redirect()
                ->route('account.orders.show', $order)
                ->with('error', 'No retryable Razorpay payment is available.');
        }

        try {
            // Create a Razorpay order if one does not already exist.
            if (!$payment->gateway_order_id) {
                $razorpayOrder = $this->api()->order->create([
                    'receipt' => $payment->payment_number,
                    'amount' => (int) round($payment->amount * 100),
                    'currency' => 'INR',
                ]);

                $payment->gateway_order_id = $razorpayOrder['id'];
            }

            // Reset a failed attempt before retrying.
            if ($payment->status === 'failed') {
                $payment->status = 'pending';
                $payment->gateway_payment_id = null;
                $payment->gateway_signature = null;
                $payment->failure_reason = null;
                $payment->paid_at = null;
            }

            $payment->save();

            return view('customer.checkout.razorpay', [
                'order' => $order,
                'payment' => $payment,
                'razorpayOrderId' => $payment->gateway_order_id,
                'razorpayKey' => config('services.razorpay.key'),
            ]);

        } catch (\Throwable $e) {
            Log::error('Razorpay order creation failed.', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('account.orders.show', $order)
                ->with('error', 'Unable to start payment. Please try again.');
        }
    }

    /**
     * Verify Razorpay signature and actual captured payment.
     */
    public function verify(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'razorpay_payment_id' => [
                'required',
                'string',
                'max:100',
            ],
            'razorpay_order_id' => [
                'required',
                'string',
                'max:100',
            ],
            'razorpay_signature' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        try {
            $payment = $order->payments()
                ->where('gateway', 'razorpay')
                ->where(
                    'gateway_order_id',
                    $validated['razorpay_order_id']
                )
                ->firstOrFail();

            if ($order->payment_status === 'paid') {
                return redirect()
                    ->route('account.orders.show', $order)
                    ->with('success', 'This order is already paid.');
            }

            // Verify Razorpay signature.
            $this->api()->utility->verifyPaymentSignature([
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature'],
            ]);

            // Fetch actual payment details from Razorpay.
            $gatewayPayment = $this->api()->payment->fetch(
                $validated['razorpay_payment_id']
            );

            if (
                $gatewayPayment['order_id'] !==
                $validated['razorpay_order_id']
            ) {
                throw new \RuntimeException('Payment order mismatch.');
            }

            if ($gatewayPayment['status'] !== 'captured') {
                return redirect()
                    ->route('account.orders.show', $order)
                    ->with(
                        'error',
                        'Payment is not captured yet. Please retry or contact support.'
                    );
            }

            // Verify amount.
            $expectedAmount = (int) round($payment->amount * 100);

            if ((int) $gatewayPayment['amount'] !== $expectedAmount) {
                throw new \RuntimeException('Payment amount mismatch.');
            }

            // Verify currency.
            if ($gatewayPayment['currency'] !== 'INR') {
                throw new \RuntimeException('Payment currency mismatch.');
            }

            /*
             * Update payment and order atomically.
             * Return true only when this request changes unpaid to paid.
             */
            $paymentCapturedNow = DB::transaction(function () use (
                $order,
                $payment,
                $validated
            ) {
                $lockedOrder = Order::whereKey($order->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $lockedPayment = Payment::whereKey($payment->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // A webhook or another request may have paid it already.
                if ($lockedOrder->payment_status === 'paid') {
                    return false;
                }

                // Never accept payment after stock release or expiry.
                if (
                    $lockedOrder->stock_released_at ||
                    $lockedOrder->status === 'cancelled' ||
                    (
                        $lockedOrder->payment_expires_at &&
                        $lockedOrder->payment_expires_at->isPast()
                    )
                ) {
                    throw new \RuntimeException(
                        'Payment reservation expired. Please contact support for a refund.'
                    );
                }

                if (
                    $lockedPayment->status === 'captured' ||
                    $lockedPayment->gateway_payment_id
                ) {
                    throw new \RuntimeException(
                        'This payment has already been processed.'
                    );
                }

                // Prevent reuse of a Razorpay payment ID.
                $alreadyUsed = Payment::where(
                    'gateway_payment_id',
                    $validated['razorpay_payment_id']
                )
                    ->where('id', '!=', $lockedPayment->id)
                    ->exists();

                if ($alreadyUsed) {
                    throw new \RuntimeException(
                        'This Razorpay payment ID has already been used.'
                    );
                }

                $lockedPayment->update([
                    'gateway_payment_id' => $validated['razorpay_payment_id'],
                    'gateway_signature' => $validated['razorpay_signature'],
                    'status' => 'captured',
                    'paid_at' => now(),
                    'failure_reason' => null,
                ]);

                $lockedOrder->update([
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'placed_at' => now(),
                ]);

                return true;
            });

            $order->refresh();

            // Send notifications only for the first successful transition.
        if ($paymentCapturedNow) {
            $finalizer = app(OrderFinalizationService::class);

            $finalizer->finalizeOrderUsage($order);
            $finalizer->sendOrderNotifications($order);
            $finalizer->removePurchasedCartItems($order);

            $this->sendPaymentNotifications($order, 'paid');
        }

            if ($order->payment_status !== 'paid') {
                return redirect()
                    ->route('account.orders.show', $order)
                    ->with('error', 'Payment requires support verification.');
            }

            return redirect()
                ->route('account.orders.show', $order)
                ->with('success', 'Razorpay payment verified successfully.');

        } catch (\Throwable $e) {
            Log::error('Razorpay payment verification failed.', [
                'order_id' => $order->id,
                'payment_id' => $validated['razorpay_payment_id'],
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('account.orders.show', $order)
                ->with(
                    'error',
                    'Payment verification failed. If money was deducted, please contact support with your order number.'
                );
        }
    }



/**
 * Verify and record an actual Razorpay payment failure.
 * Never trust a failure status supplied by the browser alone.
 */
public function failure(Request $request, Order $order)
{
    abort_unless($order->user_id === $request->user()->id, 403);

    $validated = $request->validate([
        'razorpay_payment_id' => ['required', 'string', 'max:100'],
        'razorpay_order_id' => ['required', 'string', 'max:100'],
    ]);

    try {
        $payment = $order->payments()
            ->where('gateway', 'razorpay')
            ->where('gateway_order_id', $validated['razorpay_order_id'])
            ->firstOrFail();

        // Fetch the real payment status directly from Razorpay.
        $gatewayPayment = $this->api()->payment->fetch(
            $validated['razorpay_payment_id']
        );

        if (
            $gatewayPayment['order_id'] !== $payment->gateway_order_id ||
            (int) $gatewayPayment['amount'] !==
                (int) round($payment->amount * 100) ||
            ($gatewayPayment['currency'] ?? '') !== 'INR'
        ) {
            throw new \RuntimeException('Razorpay payment details mismatch.');
        }

        if ($gatewayPayment['status'] !== 'failed') {
            return redirect()
                ->route('account.orders.show', $order)
                ->with(
                    'error',
                    'Razorpay has not confirmed a failed payment. Please check the payment status.'
                );
        }

        $failureReason = $gatewayPayment['error_description']
            ?? 'Payment failed. Please try again.';

        $failureRecorded = DB::transaction(function () use (
            $payment,
            $order,
            $gatewayPayment,
            $failureReason
        ) {
            $lockedOrder = Order::whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedPayment = Payment::whereKey($payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Never downgrade a successful payment.
            if (
                $lockedOrder->payment_status === 'paid' ||
                $lockedPayment->status === 'captured'
            ) {
                return false;
            }

            // Ignore duplicate failure callbacks.
            if ($lockedPayment->status === 'failed') {
                return false;
            }

            $lockedPayment->update([
                'gateway_payment_id' => $gatewayPayment['id'],
                'status' => 'failed',
                'failure_reason' => $failureReason,
            ]);

            $lockedOrder->update([
                'payment_status' => 'failed',
            ]);

            return true;
        });

        $order->refresh();

        if ($failureRecorded) {
            $this->sendPaymentNotifications(
                $order,
                'failed',
                $failureReason
            );
        }

        return redirect()
            ->route('account.orders.show', $order)
            ->with(
                'error',
                'Payment failed. Your order has not been confirmed. You can retry payment before the reservation expires.'
            );

    } catch (\Throwable $e) {
        Log::error('Razorpay failure verification failed.', [
            'order_id' => $order->id,
            'payment_id' => $validated['razorpay_payment_id'],
            'error' => $e->getMessage(),
        ]);

        return redirect()
            ->route('account.orders.show', $order)
            ->with(
                'error',
                'Unable to verify payment failure with Razorpay. Please refresh and check your payment status.'
            );
    }
}


    /**
     * Handle Razorpay webhook events.
     */
    public function webhook(Request $request)
    {
        $signature = $request->header('X-Razorpay-Signature');
        $secret = config('services.razorpay.webhook_secret');
        $rawBody = $request->getContent();

        if (!$signature || !$secret) {
            return response()->json([
                'message' => 'Invalid webhook configuration.',
            ], 400);
        }

        // Verify webhook HMAC signature.
        $expectedSignature = hash_hmac(
            'sha256',
            $rawBody,
            $secret
        );

        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('Invalid Razorpay webhook signature.');

            return response()->json([
                'message' => 'Invalid signature.',
            ], 400);
        }

        $event = $request->input('event');

        if (!in_array(
            $event,
            ['payment.captured', 'payment.failed'],
            true
        )) {
            return response()->json([
                'message' => 'Event ignored.',
            ], 200);
        }

        $entity = $request->input('payload.payment.entity');

        if (
            !$entity ||
            empty($entity['id']) ||
            empty($entity['order_id']) ||
            !isset($entity['amount'])
        ) {
            return response()->json([
                'message' => 'Invalid payment payload.',
            ], 400);
        }

        try {
            $payment = Payment::where('gateway', 'razorpay')
                ->where('gateway_order_id', $entity['order_id'])
                ->first();

            if (!$payment) {
                Log::warning('Razorpay webhook payment record not found.', [
                    'gateway_order_id' => $entity['order_id'],
                ]);

                return response()->json([
                    'message' => 'Payment record not found.',
                ], 200);
            }

            // Verify amount and currency against our payment record.
            if (
                (int) $entity['amount'] !==
                    (int) round($payment->amount * 100) ||
                ($entity['currency'] ?? '') !== 'INR'
            ) {
                Log::error('Razorpay webhook amount/currency mismatch.', [
                    'payment_id' => $payment->id,
                    'gateway_payment_id' => $entity['id'],
                ]);

                return response()->json([
                    'message' => 'Payment amount mismatch.',
                ], 400);
            }

            /*
             * Lock payment and order.
             * Return a status only when a real state transition occurs.
             */
            $notificationStatus = DB::transaction(function () use (
                $payment,
                $entity,
                $event
            ) {
                $lockedPayment = Payment::whereKey($payment->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $lockedOrder = Order::whereKey($lockedPayment->order_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Ignore duplicate captured events.
                if ($lockedPayment->status === 'captured') {
                    return null;
                }

                if ($event === 'payment.captured') {

                    // Do not overwrite an order paid by another payment.
                    if ($lockedOrder->payment_status === 'paid') {
                        return null;
                    }

                    // Never accept payment after stock release or expiry.
                    if (
                        $lockedOrder->stock_released_at ||
                        $lockedOrder->status === 'cancelled' ||
                        (
                            $lockedOrder->payment_expires_at &&
                            $lockedOrder->payment_expires_at->isPast()
                        )
                    ) {
                        Log::critical(
                            'Captured payment received for expired reservation.',
                            [
                                'order_id' => $lockedOrder->id,
                                'payment_id' => $lockedPayment->id,
                                'gateway_payment_id' => $entity['id'],
                            ]
                        );

                        throw new \RuntimeException(
                            'Captured payment belongs to an expired stock reservation.'
                        );
                    }

                    // Prevent payment ID reuse.
                    $alreadyUsed = Payment::where(
                        'gateway_payment_id',
                        $entity['id']
                    )
                        ->where('id', '!=', $lockedPayment->id)
                        ->exists();

                    if ($alreadyUsed) {
                        throw new \RuntimeException(
                            'Payment ID already used.'
                        );
                    }

                    $lockedPayment->update([
                        'gateway_payment_id' => $entity['id'],
                        'status' => 'captured',
                        'paid_at' => now(),
                        'failure_reason' => null,
                    ]);

                    $lockedOrder->update([
                        'status' => 'confirmed',
                        'payment_status' => 'paid',
                        'placed_at' => now(),
                    ]);

                    return 'paid';
                }

                if ($event === 'payment.failed') {

                    // Never downgrade a paid order.
                    if ($lockedOrder->payment_status === 'paid') {
                        return null;
                    }

                    // Ignore duplicate failure events for this attempt.
                    if ($lockedPayment->status === 'failed') {
                        return null;
                    }

                    $reason = $entity['error_description']
                        ?? 'Payment failed.';

                    $lockedPayment->update([
                        'status' => 'failed',
                        'failure_reason' => $reason,
                    ]);

                    $lockedOrder->update([
                        'payment_status' => 'failed',
                    ]);

                    return 'failed';
                }

                return null;
            });

            // Notify only after the transaction has committed.
            if ($notificationStatus !== null) {
                $order = Order::findOrFail($payment->order_id);

                if ($notificationStatus === 'paid') {
                    $finalizer = app(OrderFinalizationService::class);

                    $finalizer->finalizeOrderUsage($order);
                    $finalizer->sendOrderNotifications($order);
                    $finalizer->removePurchasedCartItems($order);
                }

                $this->sendPaymentNotifications(
                    $order,
                    $notificationStatus,
                    $notificationStatus === 'failed'
                        ? ($entity['error_description'] ?? 'Payment failed.')
                        : null
                );
            }

            return response()->json([
                'message' => 'Webhook processed.',
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Razorpay webhook processing failed.', [
                'payment_id' => $payment->id ?? null,
                'gateway_payment_id' => $entity['id'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Webhook processing failed.',
            ], 500);
        }
    }

    /**
     * Send payment status notifications.
     *
     * Successful payment:
     * Customer + Vendors + Admins
     *
     * Failed payment:
     * Customer + Admins
     */
    private function sendPaymentNotifications(
        Order $order,
        string $status,
        ?string $reason = null
    ): void {
        try {
            $order->loadMissing([
                'user',
                'items.vendor.user',
            ]);

            // Customer notification.
            if ($order->user) {
                try {
                    $order->user->notify(
                        new PaymentStatusNotification(
                            $status,
                            $order->order_number,
                            $reason,
                            'customer'
                        )
                    );
                } catch (\Throwable $e) {
                    Log::error('Customer payment notification failed.', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Successful payments notify all vendors in the order.
            if ($status === 'paid') {
                $vendors = $order->items
                    ->pluck('vendor')
                    ->filter()
                    ->unique('id');

                foreach ($vendors as $vendor) {
                    if (!$vendor->user) {
                        continue;
                    }

                    try {
                        $vendor->user->notify(
                            new PaymentStatusNotification(
                                'paid',
                                $order->order_number,
                                null,
                                'vendor'
                            )
                        );
                    } catch (\Throwable $e) {
                        Log::error('Vendor payment notification failed.', [
                            'order_id' => $order->id,
                            'vendor_id' => $vendor->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            // Notify all active admins of success or failure.
            $admins = User::where('status', 'active')
                ->whereHas('roles', function ($query) {
                    $query->where('slug', 'admin');
                })
                ->get();

            foreach ($admins as $admin) {
                try {
                    $admin->notify(
                        new PaymentStatusNotification(
                            $status,
                            $order->order_number,
                            $reason,
                            'admin'
                        )
                    );
                } catch (\Throwable $e) {
                    Log::error('Admin payment notification failed.', [
                        'order_id' => $order->id,
                        'admin_id' => $admin->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

        } catch (\Throwable $e) {
            // Notification failure must not undo a verified payment.
            Log::error('Payment notification processing failed.', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
