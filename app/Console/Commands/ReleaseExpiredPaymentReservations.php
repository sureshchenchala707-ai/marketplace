<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReleaseExpiredPaymentReservations extends Command
{
    protected $signature = 'orders:release-expired-reservations';

    protected $description = 'Release stock reserved by expired unpaid Razorpay orders';

    public function handle(): int
    {
        $expiredOrderIds = Order::query()
            ->where('payment_status', '!=', 'paid')
            ->where('status', 'pending')
            ->where('payment_expires_at', '<=', now())
            ->whereNull('stock_released_at')
            ->orderBy('id')
            ->limit(100)
            ->pluck('id');

        $released = 0;

        foreach ($expiredOrderIds as $orderId) {
            try {
                $wasReleased = DB::transaction(function () use ($orderId) {

                    $order = Order::whereKey($orderId)
                        ->lockForUpdate()
                        ->first();

                    if (
                        !$order ||
                        $order->payment_status === 'paid' ||
                        $order->status !== 'pending' ||
                        !$order->payment_expires_at ||
                        $order->payment_expires_at->isFuture() ||
                        $order->stock_released_at
                    ) {
                        return false;
                    }

                    $payment = Payment::where('order_id', $order->id)
                        ->where('gateway', 'razorpay')
                        ->lockForUpdate()
                        ->latest('id')
                        ->first();

                    if (!$payment || $payment->status === 'captured') {
                        return false;
                    }

                    $items = OrderItem::where('order_id', $order->id)
                        ->lockForUpdate()
                        ->get();

                    foreach ($items as $item) {

                        if ($item->product_variant_id) {

                            $variant = DB::table('product_variants')
                                ->where('id', $item->product_variant_id)
                                ->lockForUpdate()
                                ->first();

                            if ($variant) {
                                DB::table('product_variants')
                                    ->where('id', $variant->id)
                                    ->increment('stock_quantity', $item->quantity);
                            }

                        } elseif ($item->product_id) {

                            $product = DB::table('products')
                                ->where('id', $item->product_id)
                                ->lockForUpdate()
                                ->first();

                            if ($product) {
                                DB::table('products')
                                    ->where('id', $product->id)
                                    ->increment('stock_quantity', $item->quantity);
                            }
                        }
                    }

                    $order->stock_released_at = now();
                    $order->payment_status = 'failed';
                    $order->status = 'cancelled';
                    $order->cancelled_at = now();
                    $order->save();

                    if (in_array($payment->status, ['created', 'pending'], true)) {
                        $payment->status = 'failed';
                        $payment->failure_reason = 'Payment reservation expired.';
                        $payment->save();
                    }

                    return true;
                });

                if ($wasReleased) {
                    $released++;
                }

            } catch (\Throwable $e) {
                Log::error('Failed to release expired order stock.', [
                    'order_id' => $orderId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Released stock for {$released} expired order(s).");

        return self::SUCCESS;
    }
}