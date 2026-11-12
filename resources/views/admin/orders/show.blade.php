
@extends('layouts.app')

@section('title', 'Order Details - ESS MARKETPLACE')

@section('content')
<div class="container-fluid px-lg-5 py-5">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Order Details</h2>
            <div class="text-primary fw-bold">{{ $order->order_number }}</div>
        </div>

        <a href="{{ route('admin.orders.index') }}"
           class="btn btn-light">
            <i class="fas fa-arrow-left me-2"></i>Back to Orders
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">

        {{-- ORDER AND CUSTOMER --}}
        <div class="col-lg-4">

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">Order Information</h5>

                    <div class="mb-2">
                        <small class="text-muted">Order Number</small>
                        <div class="fw-bold">{{ $order->order_number }}</div>
                    </div>

                    <div class="mb-2">
                        <small class="text-muted">Order Date</small>
                        <div>{{ $order->placed_at?->format('d M Y, h:i A') }}</div>
                    </div>

                    <div class="mb-2">
                        <small class="text-muted">Current Status</small>
                        <div>
                            <span class="badge bg-primary">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-2">
                        <small class="text-muted">Payment Status</small>
                        <div>
                            <span class="badge bg-light text-dark border">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>

                    <hr>

                    <h6 class="fw-bold">Update Overall Order Status</h6>

                    @php
                        $nextStatuses = [
                            'pending' => ['confirmed'],
                            'confirmed' => ['processing'],
                            'processing' => ['shipped'],
                            'shipped' => ['delivered'],
                            'delivered' => [],
                            'cancelled' => [],
                            'returned' => [],
                        ];
                    @endphp

                    @if(count($nextStatuses[$order->status] ?? []) > 0)
                        <form method="POST"
                              action="{{ route('admin.orders.status', $order) }}">
                            @csrf
                            @method('PATCH')

                            <label class="form-label">Next Status</label>

                            <select name="status" class="form-select mb-3" required>
                                @foreach($nextStatuses[$order->status] as $nextStatus)
                                    <option value="{{ $nextStatus }}">
                                        {{ ucfirst($nextStatus) }}
                                    </option>
                                @endforeach
                            </select>

                            <button class="btn btn-primary w-100">
                                Update Order Status
                            </button>
                        </form>
                    @else
                        <p class="text-muted small mb-0">
                            This order is in a final status.
                        </p>
                    @endif

                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">Customer Details</h5>

                    <div class="fw-bold">{{ $order->shipping_name }}</div>

                    <div class="text-muted small">
                        {{ $order->user->email ?? 'N/A' }}<br>
                        {{ $order->shipping_phone }}
                    </div>

                    <hr>

                    <h6 class="fw-bold">Shipping Address</h6>

                    <p class="text-muted mb-0">
                        {{ $order->shipping_address_line1 }}<br>

                        @if($order->shipping_address_line2)
                            {{ $order->shipping_address_line2 }}<br>
                        @endif

                        {{ $order->shipping_city }},
                        {{ $order->shipping_state }} -
                        {{ $order->shipping_pincode }}<br>

                        {{ $order->shipping_country }}
                    </p>

                    @if($order->customer_note)
                        <hr>
                        <h6 class="fw-bold">Customer Note</h6>
                        <p class="text-muted mb-0">{{ $order->customer_note }}</p>
                    @endif

                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">Payment Records</h5>

                    @forelse($order->payments as $payment)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="fw-bold">{{ $payment->payment_number }}</div>
                            <div class="small text-muted">
                                Gateway: {{ strtoupper($payment->gateway) }}
                            </div>
                            <div class="small text-muted">
                                Status: {{ ucfirst($payment->status) }}
                            </div>
                            <div class="fw-semibold">
                                ₹{{ number_format($payment->amount, 2) }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No payment records found.</p>
                    @endforelse

                    @if(
    $order->status === 'delivered' &&
    $order->payment_status !== 'paid' &&
    $order->payments->contains('gateway', 'cod')
)
    <form method="POST"
          action="{{ route('admin.orders.cod-payment-received', $order) }}"
          onsubmit="return confirm('Confirm that COD payment has been collected?')">
        @csrf
        @method('PATCH')

        <button type="submit" class="btn btn-success">
            <i class="fas fa-money-bill-wave me-2"></i>
            Mark COD Payment as Received
        </button>
    </form>
@endif

                </div>
            </div>

        </div>

        {{-- ORDER ITEMS --}}
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-4">Ordered Products & Vendor Fulfillment</h5>

                    @foreach($order->items as $item)
                        <div class="border rounded-3 p-3 mb-3">

                            <div class="d-flex flex-wrap gap-3 align-items-start">

                                @if($item->product_image)
                                    <img src="{{ asset('storage/' . $item->product_image) }}"
                                         alt="{{ $item->product_name }}"
                                         style="width:80px;height:80px;object-fit:cover;border-radius:10px">
                                @endif

                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1">
                                        {{ $item->product_name }}
                                    </h6>

                                    @if($item->variant_name)
                                        <div class="small text-muted">
                                            Variant: {{ $item->variant_name }}
                                        </div>
                                    @endif

                                    <div class="small text-muted">
                                        SKU: {{ $item->product_sku ?? 'N/A' }}
                                    </div>

                                    <div class="small">
                                        Vendor:
                                        <strong>{{ $item->vendor->shop_name ?? 'N/A' }}</strong>
                                    </div>

                                    <div class="small text-muted">
                                        Quantity: {{ $item->quantity }}
                                    </div>
                                </div>

                                <div class="text-end">
                                    <div class="fw-bold text-primary">
                                        ₹{{ number_format($item->total_amount, 2) }}
                                    </div>

                                    <span class="badge bg-light text-dark border mt-2">
                                        {{ ucfirst($item->vendor_status) }}
                                    </span>
                                </div>

                            </div>

                            @if($item->tracking_number)
                                <div class="alert alert-info mt-3 mb-0 small">
                                    Carrier: {{ $item->shipping_carrier }}<br>
                                    Tracking: {{ $item->tracking_number }}<br>

                                    @if($item->shipped_at)
                                        Shipped: {{ $item->shipped_at->format('d M Y, h:i A') }}
                                    @endif

                                    @if($item->delivered_at)
                                        <br>Delivered: {{ $item->delivered_at->format('d M Y, h:i A') }}
                                    @endif
                                </div>
                            @endif

                        </div>
                    @endforeach

                </div>
            </div>

            {{-- TOTALS --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">Order Amount Summary</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong>₹{{ number_format($order->subtotal, 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Discount</span>
                        <strong>₹{{ number_format($order->discount_amount, 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping</span>
                        <strong>₹{{ number_format($order->shipping_amount, 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax</span>
                        <strong>₹{{ number_format($order->tax_amount, 2) }}</strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <h5 class="fw-bold">Grand Total</h5>
                        <h5 class="fw-bold text-primary">
                            ₹{{ number_format($order->total_amount, 2) }}
                        </h5>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>
@endsection
