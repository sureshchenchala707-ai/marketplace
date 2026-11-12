
@extends('layouts.app')

@section('title', 'Vendor Orders - ESS MARKETPLACE')

@section('content')
<div class="container-fluid px-lg-5 py-5">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Order Management</h2>
            <p class="text-muted mb-0">Manage orders containing your products.</p>
        </div>
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

    {{-- SUMMARY --}}
    <div class="row g-3 mb-4">
        @foreach([
            'Total Items' => $stats['total'],
            'Pending' => $stats['pending'],
            'Processing' => $stats['processing'],
            'Shipped' => $stats['shipped'],
            'Delivered' => $stats['delivered'],
        ] as $label => $count)
            <div class="col-6 col-md">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ $label }}</div>
                        <div class="fs-3 fw-bold text-primary">{{ $count }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- FILTER --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="d-flex flex-wrap gap-2">
                <select name="status" class="form-select" style="max-width:240px">
                    <option value="">All Item Statuses</option>
                    @foreach(['pending','processing','shipped','delivered','cancelled'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>

                <button class="btn btn-primary">Filter</button>
                <a href="{{ route('vendor.orders.index') }}"
                   class="btn btn-light">Reset</a>
            </form>
        </div>
    </div>

    {{-- ORDERS --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="p-3">Order Number</th>
                            <th>Customer</th>
                            <th>My Products</th>
                            <th>My Items Status</th>
                            <th>Order Date</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="p-3 fw-bold text-primary">
                                    {{ $order->order_number }}
                                </td>

                                <td>
                                    {{ $order->user->name ?? 'Customer' }}
                                </td>

                                <td>
                                    {{ $order->items->count() }} item(s)
                                </td>

                                <td>
                                    @foreach($order->items->pluck('vendor_status')->unique() as $status)
                                        <span class="badge bg-light text-dark border mb-1">
                                            {{ ucfirst($status) }}
                                        </span>
                                    @endforeach
                                </td>

                                <td>
                                    {{ $order->placed_at?->format('d M Y') }}
                                </td>

                                <td class="text-end pe-3">
                                    <a href="{{ route('vendor.orders.show', $order) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        View Order
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-box-open fa-2x text-muted mb-3"></i>
                                    <h5>No orders yet</h5>
                                    <p class="text-muted mb-0">
                                        Orders containing your products will appear here.
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
