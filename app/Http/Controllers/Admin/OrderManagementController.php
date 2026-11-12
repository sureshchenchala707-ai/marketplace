<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')
            ->withCount('items')
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('shipping_name', 'like', "%{$search}%")
                    ->orWhere('shipping_phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load([
            'user',
            'items.vendor.user',
            'items.product',
            'items.variant',
            'payments',
        ]);

        return view('admin.orders.show', compact('order'));
    }


    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:confirmed,processing,shipped,delivered',
            ],
        ]);

        try {
            DB::transaction(function () use ($order, $validated) {

                $lockedOrder = Order::whereKey($order->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $current = $lockedOrder->status;
                $next = $validated['status'];

                $allowedTransitions = [
                    'pending' => ['confirmed'],
                    'confirmed' => ['processing'],
                    'processing' => ['shipped'],
                    'shipped' => ['delivered'],
                    'delivered' => [],
                    'cancelled' => [],
                    'returned' => [],
                ];

                if (!in_array($next, $allowedTransitions[$current] ?? [], true)) {
                    throw ValidationException::withMessages([
                        'status' => "Order cannot move from {$current} to {$next}.",
                    ]);
                }

                // All vendor items must be shipped before admin marks order shipped.
                if ($next === 'shipped') {

                    $allItemsShipped = $lockedOrder->items()
                        ->whereNotIn('vendor_status', ['shipped', 'delivered'])
                        ->doesntExist();

                    if (!$allItemsShipped) {
                        throw ValidationException::withMessages([
                            'status' => 'All vendor items must be shipped before updating the order.',
                        ]);
                    }
                }

                // All vendor items must be delivered before admin marks order delivered.
                if ($next === 'delivered') {

                    $allItemsDelivered = $lockedOrder->items()
                        ->where(function ($query) {
                            $query->whereNull('vendor_status')
                                ->orWhere('vendor_status', '!=', 'delivered');
                        })
                        ->doesntExist();

                    if (!$allItemsDelivered) {
                        throw ValidationException::withMessages([
                            'status' => 'All vendor items must be delivered before completing the order.',
                        ]);
                    }
                }

                $lockedOrder->status = $next;

                if ($next === 'delivered') {
                    $lockedOrder->delivered_at = now();
                }

                $lockedOrder->save();
            });

        } catch (ValidationException $e) {
            throw $e;
        }

        return back()->with('success', 'Order status updated successfully.');
    }



public function markCodPaymentReceived(Order $order)
{
    try {
        DB::transaction(function () use ($order) {

            $lockedOrder = Order::whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedOrder->status !== 'delivered') {
                throw ValidationException::withMessages([
                    'payment' => 'COD payment can be collected only after order delivery.',
                ]);
            }

            $payment = $lockedOrder->payments()
                ->where('gateway', 'cod')
                ->lockForUpdate()
                ->first();

            if (!$payment) {
                throw ValidationException::withMessages([
                    'payment' => 'COD payment record not found.',
                ]);
            }

            if ($payment->status === 'captured' ||
                $lockedOrder->payment_status === 'paid') {
                throw ValidationException::withMessages([
                    'payment' => 'This order payment is already marked as received.',
                ]);
            }

            if (!in_array($payment->status, ['created', 'pending'], true)) {
                throw ValidationException::withMessages([
                    'payment' => 'This COD payment cannot be marked as received.',
                ]);
            }

            $payment->status = 'captured';
            $payment->paid_at = now();
            $payment->save();

            $lockedOrder->payment_status = 'paid';
            $lockedOrder->save();
        });

    } catch (ValidationException $e) {
        throw $e;
    }

    return back()->with('success', 'COD payment marked as received successfully.');
}


}
