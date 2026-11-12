
@extends('layouts.app')

@section('title', 'Order Details - ESS MARKETPLACE')

@section('content')

@php
    $latestPayment = $order->latestPayment;

    $isRazorpay = $latestPayment?->gateway === 'razorpay';
    $isPaid = $order->payment_status === 'paid';
    $isFailed = $isRazorpay && $order->payment_status === 'failed';

    $isPaymentPending = $isRazorpay
        && !in_array($order->payment_status, ['paid', 'failed'], true);

    $canRetryPayment = $isRazorpay
        && !$isPaid
        && $order->status === 'pending'
        && !$order->stock_released_at
        && $order->payment_expires_at
        && now()->lt($order->payment_expires_at)
        && in_array($latestPayment?->status, ['created', 'pending', 'failed'], true);
@endphp

<div class="container py-5">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- ORDER / PAYMENT STATUS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">

            @if($isFailed)

                <h2 class="fw-bold text-danger">
                    <i class="fas fa-times-circle me-2"></i>
                    Payment Failed
                </h2>

                <p class="text-muted">
                    Your payment was unsuccessful. Your order has not been confirmed.
                </p>

            @elseif($isPaymentPending)

                <h2 class="fw-bold text-warning">
                    <i class="fas fa-clock me-2"></i>
                    Payment Pending
                </h2>

                <p class="text-muted">
                    Complete your payment to confirm your order.
                </p>

            @elseif($isPaid || (!$isRazorpay && $order->status === 'confirmed'))

                <h2 class="fw-bold text-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Order Placed Successfully!
                </h2>

                <p class="text-muted">
                    Your order has been confirmed successfully.
                </p>

            @else

                <h2 class="fw-bold">
                    Order Status: {{ ucfirst($order->status) }}
                </h2>

                <p class="text-muted">
                    Your order is not yet confirmed.
                </p>

            @endif

            <p class="text-muted mb-1">Order Number</p>

            <h5 class="text-primary fw-bold">
                {{ $order->order_number }}
            </h5>

            <div class="mt-3">

                <span class="badge
                    {{ $order->status === 'confirmed' ? 'bg-success' : 'bg-secondary' }}">
                    {{ ucfirst($order->status) }}
                </span>

                <span class="badge
                    {{ $isPaid ? 'bg-success' : ($isFailed ? 'bg-danger' : 'bg-warning text-dark') }}">
                    Payment: {{ ucfirst($order->payment_status) }}
                </span>

            </div>

            <h4 class="fw-bold mt-4">
                Total: ₹{{ number_format($order->total_amount, 2) }}
            </h4>

            @if($isFailed)

                <div class="alert alert-danger mt-3 mb-0">
                    Payment failed. Please retry before your stock reservation expires.
                </div>

            @elseif($isPaymentPending)

                <div class="alert alert-warning mt-3 mb-0">
                    Your order is awaiting payment confirmation.
                </div>

            @endif

            @if($canRetryPayment)

                <a href="{{ route('account.orders.razorpay.initiate', $order) }}"
                   class="btn btn-primary btn-lg mt-3">
                    <i class="fas fa-redo me-2"></i>
                    Retry Payment
                </a>

                <p class="small text-muted mt-2 mb-0">
                    Payment reservation expires:
                    {{ $order->payment_expires_at->format('d M Y, h:i A') }}
                </p>

            @elseif($isRazorpay && !$isPaid)

                <div class="alert alert-secondary mt-3 mb-0">
                    Payment retry is unavailable because this reservation has expired
                    or the payment is no longer retryable. Please contact support.
                </div>

            @endif

        </div>
    </div>

    {{-- ORDERED PRODUCTS --}}
    <div class="row g-4">

        <div class="col-lg-7">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body p-4">

                    <h5 class="fw-bold mb-4">Ordered Products</h5>

                    @foreach($order->items as $item)

                        <div class="d-flex gap-3 border-bottom pb-3 mb-3">

                            @if($item->product_image)
                                <img src="{{ asset('storage/' . $item->product_image) }}"
                                     alt="{{ $item->product_name }}"
                                     style="width:75px;height:75px;object-fit:cover;border-radius:10px">
                            @endif

                            <div class="flex-grow-1">

                                <h6 class="fw-bold">
                                    {{ $item->product_name }}
                                </h6>

                                @if($item->variant_name)
                                    <small class="text-muted">
                                        {{ $item->variant_name }}
                                    </small>
                                @endif

                                <div class="small text-muted">
                                    Quantity: {{ $item->quantity }}
                                </div>

                            </div>

                            <strong>
                                ₹{{ number_format($item->total_amount, 2) }}
                            </strong>

                        </div>

                        <div class="mt-3 p-3 rounded-3"
                             style="background:#f5f8ff;">

                            <h6 class="fw-bold mb-2">
                                Vendor & Shipping Details
                            </h6>

                            <p class="mb-1">
                                <strong>Vendor:</strong>
                                {{ $item->vendor?->shop_name ?? 'Vendor information unavailable' }}
                            </p>

                            <p class="mb-1">
                                <strong>Item Status:</strong>
                                {{ ucfirst($item->vendor_status ?? 'pending') }}
                            </p>

                            @if($item->shipping_carrier)
                                <p class="mb-1">
                                    <strong>Courier:</strong>
                                    {{ $item->shipping_carrier }}
                                </p>
                            @endif

                            @if($item->tracking_number)
                                <p class="mb-0">
                                    <strong>Tracking Number:</strong>
                                    {{ $item->tracking_number }}
                                </p>
                            @endif

                        </div>

                    @endforeach

                </div>

            </div>

        </div>

        {{-- DELIVERY ADDRESS AND TOTAL --}}
        <div class="col-lg-5">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">
                        Delivery Address
                    </h5>

                    <strong>{{ $order->shipping_name }}</strong>

                    <p class="text-muted mt-2">
                        {{ $order->shipping_phone }}<br>
                        {{ $order->shipping_address_line1 }}<br>

                        @if($order->shipping_address_line2)
                            {{ $order->shipping_address_line2 }}<br>
                        @endif

                        {{ $order->shipping_city }},
                        {{ $order->shipping_state }} -
                        {{ $order->shipping_pincode }}<br>

                        {{ $order->shipping_country }}
                    </p>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <span>Subtotal</span>
                        <strong>
                            ₹{{ number_format($order->subtotal, 2) }}
                        </strong>
                    </div>

                    <div class="d-flex justify-content-between mt-2">
                        <span>Discount</span>
                        <strong>
                            ₹{{ number_format($order->discount_amount, 2) }}
                        </strong>
                    </div>

                    <div class="d-flex justify-content-between mt-2">
                        <span>Shipping</span>
                        <strong>
                            ₹{{ number_format($order->shipping_amount, 2) }}
                        </strong>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <strong>Total</strong>
                        <strong class="text-primary">
                            ₹{{ number_format($order->total_amount, 2) }}
                        </strong>
                    </div>

                    <div class="alert
                        {{ $isPaid ? 'alert-success' : ($isFailed ? 'alert-danger' : 'alert-warning') }}
                        mt-3 mb-0">

                        Payment Status:
                        <strong>{{ ucfirst($order->payment_status) }}</strong>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <a href="{{ route('account.orders.index') }}"
       class="btn btn-primary mt-4">
        My Orders
    </a>

    <a href="{{ route('home') }}"
       class="btn btn-light mt-4">
        Continue Shopping
    </a>

</div>

@endsection
