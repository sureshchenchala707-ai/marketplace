
@extends('layouts.app')

@section('title', 'Order Management - ESS MARKETPLACE')

@section('content')
<div class="container-fluid px-lg-5 py-5">

    <div class="mb-4">
        <h2 class="fw-bold mb-1">Order Management</h2>
        <p class="text-muted mb-0">Monitor and manage all marketplace orders.</p>
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

    {{-- STATISTICS --}}
    <div class="row g-3 mb-4">
        @foreach([
            'Total Orders' => $stats['total'],
            'Pending' => $stats['pending'],
            'Confirmed' => $stats['confirmed'],
            'Processing' => $stats['processing'],
            'Shipped' => $stats['shipped'],
            'Delivered' => $stats['delivered'],
        ] as $label => $count)
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ $label }}</div>
                        <div class="fs-3 fw-bold text-primary">{{ $count }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- FILTERS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2">

                <div class="col-lg-5">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search order number, customer, email or phone..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3 col-lg-2">
                    <select name="status" class="form-select">
                        <option value="">All Order Statuses</option>
                        @foreach(['pending','confirmed','processing','shipped','delivered','cancelled','returned'] as $status)
                            <option value="{{ $status }}"
                                @selected(request('status') === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-lg-2">
                    <select name="payment_status" class="form-select">
                        <option value="">All Payment Statuses</option>
                        @foreach(['pending','paid','failed','refunded','partially_refunded'] as $status)
                            <option value="{{ $status }}"
                                @selected(request('payment_status') === $status)>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-lg-3 d-flex gap-2">
                    <button class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.orders.index') }}"
                       class="btn btn-light">Reset</a>
                </div>

            </form>
        </div>
    </div>

    {{-- ORDERS TABLE --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="p-3">Order Number</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Order Status</th>
                            <th>Payment</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="p-3">
                                    <strong class="text-primary">
                                        {{ $order->order_number }}
                                    </strong>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $order->shipping_name }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $order->user->email ?? '' }}
                                    </small>
                                </td>

                                <td>{{ $order->items_count }}</td>

                                <td class="fw-bold">
                                    ₹{{ number_format($order->total_amount, 2) }}
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $order->placed_at?->format('d M Y') }}
                                </td>

                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-box-open fa-2x text-muted mb-3"></i>
                                    <h5>No orders found</h5>
                                    <p class="text-muted mb-0">
                                        Orders will appear here when customers place them.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>

</div>
@endsection
