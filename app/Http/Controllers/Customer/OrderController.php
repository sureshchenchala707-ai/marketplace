<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()
            ->orders()
            ->where('status', 'confirmed')
            ->with(['items', 'latestPayment'])
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load([
            'items.vendor.user',
            'items.product',
            'items.variant',
            'latestPayment',
        ]);

        return view('customer.orders.show', compact('order'));
    }
}
