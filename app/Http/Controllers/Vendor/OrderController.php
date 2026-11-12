<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Only confirmed orders are visible to vendors.
     * Pending Razorpay orders must not enter vendor fulfilment.
     */
    private function confirmedOrders()
    {
        return Order::where('status', 'confirmed');
    }

    public function index(Request $request)
    {
        $vendorId = $request->user()->vendor->id;

        $query = $this->confirmedOrders()
            ->whereHas('items', function ($q) use ($vendorId, $request) {
                $q->where('vendor_id', $vendorId);

                if ($request->filled('status')) {
                    $q->where('vendor_status', $request->status);
                }
            })
            ->with([
                'user',
                'items' => function ($q) use ($vendorId) {
                    $q->where('vendor_id', $vendorId);
                },
            ])
            ->latest();

        $orders = $query->paginate(15)->withQueryString();

        $base = OrderItem::where('vendor_id', $vendorId)
            ->whereHas('order', function ($q) {
                $q->where('status', 'confirmed');
            });

        $stats = [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)
                ->where('vendor_status', 'pending')->count(),
            'processing' => (clone $base)
                ->where('vendor_status', 'processing')->count(),
            'shipped' => (clone $base)
                ->where('vendor_status', 'shipped')->count(),
            'delivered' => (clone $base)
                ->where('vendor_status', 'delivered')->count(),
        ];

        return view('vendor.orders.index', compact('orders', 'stats'));
    }

    public function show(Request $request, Order $order)
    {
        $vendorId = $request->user()->vendor->id;

        abort_unless($order->status === 'confirmed', 404);

        $items = $order->items()
            ->where('vendor_id', $vendorId)
            ->get();

        abort_if($items->isEmpty(), 404);

        return view('vendor.orders.show', compact('order', 'items'));
    }

    public function updateItemStatus(Request $request, OrderItem $orderItem)
    {
        $vendorId = $request->user()->vendor->id;

        abort_unless($orderItem->vendor_id === $vendorId, 403);

        // Block fulfilment until the order is confirmed.
        abort_unless(
            $orderItem->order()->where('status', 'confirmed')->exists(),
            403,
            'This order is not yet confirmed for fulfilment.'
        );

        $validated = $request->validate([
            'vendor_status' => [
                'required',
                'in:processing,shipped,delivered,cancelled',
            ],
            'shipping_carrier' => [
                'nullable',
                'required_if:vendor_status,shipped',
                'string',
                'max:100',
            ],
            'tracking_number' => [
                'nullable',
                'required_if:vendor_status,shipped',
                'string',
                'max:150',
            ],
        ]);

        $current = $orderItem->vendor_status;
        $next = $validated['vendor_status'];

        $allowedTransitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered'],
            'delivered' => [],
            'cancelled' => [],
        ];

        if (!in_array($next, $allowedTransitions[$current] ?? [], true)) {
            throw ValidationException::withMessages([
                'vendor_status' => 'This order item cannot move from '
                    . $current . ' to ' . $next . '.',
            ]);
        }

        $orderItem->vendor_status = $next;

        if ($next === 'shipped') {
            $orderItem->shipping_carrier = $validated['shipping_carrier'];
            $orderItem->tracking_number = $validated['tracking_number'];
            $orderItem->shipped_at = now();
        }

        if ($next === 'delivered') {
            $orderItem->delivered_at = now();
        }

        $orderItem->save();

        return back()->with(
            'success',
            'Order item status updated successfully.'
        );
    }
}
