
@extends('layouts.app')

@section('title', 'Order Details - ESS MARKETPLACE')

@section('content')
<div class="container-fluid px-lg-5 py-5">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Order Details</h2>
            <p class="text-primary fw-bold mb-0">{{ $order->order_number }}</p>
        </div>

        <a href="{{ route('vendor.orders.index') }}"
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

        {{-- CUSTOMER DETAILS --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Delivery Details</h5>

                    <div class="fw-bold">{{ $order->shipping_name }}</div>
                    <div class="text-muted small mb-2">
                        {{ $order->shipping_phone }}
                    </div>

                    <p class="mb-0">
                        {{ $order->shipping_address_line1 }}<br>

                        @if($order->shipping_address_line2)
                            {{ $order->shipping_address_line2 }}<br>
                        @endif

                        {{ $order->shipping_city }},
                        {{ $order->shipping_state }} -
                        {{ $order->shipping_pincode }}<br>

                        {{ $order->shipping_country }}
                    </p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Order Information</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Order Date</span>
                        <strong>{{ $order->placed_at?->format('d M Y') }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Order Status</span>
                        <strong>{{ ucfirst($order->status) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Payment Status</span>
                        <strong>{{ ucfirst($order->payment_status) }}</strong>
                    </div>

                    @if($order->customer_note)
                        <hr>
                        <strong>Customer Note</strong>
                        <p class="text-muted mb-0">{{ $order->customer_note }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- VENDOR PRODUCTS --}}
        <div class="col-lg-8">
            @foreach($items as $item)
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">

                        <div class="d-flex flex-wrap gap-3 align-items-start mb-4">

                            @if($item->product_image)
                                <img src="{{ asset('storage/' . $item->product_image) }}"
                                     alt="{{ $item->product_name }}"
                                     style="width:85px;height:85px;object-fit:cover;border-radius:12px">
                            @endif

                            <div class="flex-grow-1">
                                <h5 class="fw-bold">{{ $item->product_name }}</h5>

                                @if($item->variant_name)
                                    <div class="text-muted small">
                                        Variant: {{ $item->variant_name }}
                                    </div>
                                @endif

                                <div class="text-muted small">
                                    SKU: {{ $item->product_sku ?? 'N/A' }}
                                </div>

                                <div class="mt-2">
                                    Qty: {{ $item->quantity }}
                                    <span class="mx-2">|</span>
                                    ₹{{ number_format($item->total_amount, 2) }}
                                </div>
                            </div>

                            <span class="badge bg-primary">
                                {{ ucfirst($item->vendor_status) }}
                            </span>

                        </div>

                        @if($item->tracking_number)
                            <div class="alert alert-info">
                                Carrier: {{ $item->shipping_carrier }}<br>
                                Tracking Number: {{ $item->tracking_number }}
                            </div>
                        @endif

                        @if(!in_array($item->vendor_status, ['delivered', 'cancelled'], true))
                            <hr>

                            <h6 class="fw-bold mb-3">Update Fulfillment Status</h6>

                            <form method="POST"
                                  action="{{ route('vendor.orders.items.status', $item) }}">
                                @csrf
                                @method('PATCH')

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label class="form-label">New Status</label>

                                        <select name="vendor_status"
                                                class="form-select"
                                                required
                                                data-shipping-select>
                                            @if($item->vendor_status === 'pending')
                                                <option value="processing">Processing</option>
                                                <option value="cancelled">Cancelled</option>
                                            @elseif($item->vendor_status === 'processing')
                                                <option value="shipped">Shipped</option>
                                                <option value="cancelled">Cancelled</option>
                                            @elseif($item->vendor_status === 'shipped')
                                                <option value="delivered">Delivered</option>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Courier / Carrier</label>
                                        <input type="text"
                                               name="shipping_carrier"
                                               class="form-control"
                                               maxlength="100"
                                               value="{{ $item->shipping_carrier }}"
                                               placeholder="e.g. Delhivery, Blue Dart">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Tracking Number</label>
                                        <input type="text"
                                               name="tracking_number"
                                               class="form-control"
                                               maxlength="150"
                                               value="{{ $item->tracking_number }}"
                                               placeholder="Enter tracking number">
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>
                                            Update Status
                                        </button>
                                    </div>

                                </div>
                            </form>
                        @else
                            <div class="text-muted small">
                                This item has reached its final status.
                            </div>
                        @endif

                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
