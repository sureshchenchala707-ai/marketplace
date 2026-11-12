
@extends('layouts.app')

@section('title', 'My Orders - ESS MARKETPLACE')

@section('content')
<div class="container py-5">

    <h2 class="fw-bold mb-2">My Orders</h2>
    <p class="text-muted mb-4">View and track your purchases.</p>

    @forelse($orders as $order)
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-4">

                <div class="row align-items-center g-3">

                    <div class="col-md-3">
                        <small class="text-muted">Order Number</small>
                        <div class="fw-bold">{{ $order->order_number }}</div>
                    </div>

                    <div class="col-md-2">
                        <small class="text-muted">Order Date</small>
                        <div>{{ $order->placed_at?->format('d M Y') }}</div>
                    </div>

                    <div class="col-md-2">
                        <small class="text-muted">Amount</small>
                        <div class="fw-bold text-primary">
                            ₹{{ number_format($order->total_amount, 2) }}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <small class="text-muted">Status</small>
                        <div>
                            <span class="badge bg-success">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="col-md-3 text-md-end">
                        <a href="{{ route('account.orders.show', $order) }}"
                           class="btn btn-outline-primary btn-sm">
                            View Details
                        </a>
                    </div>

                </div>

            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm rounded-4 text-center p-5">
            <i class="fas fa-box-open fa-3x text-primary mb-3"></i>
            <h5>No orders yet</h5>
            <p class="text-muted">Your placed orders will appear here.</p>

            <a href="{{ route('home') }}" class="btn btn-primary mx-auto">
                Start Shopping
            </a>
        </div>
    @endforelse

    {{ $orders->links() }}

</div>
@endsection
