
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1">Coupon Management</h3>
            <p class="text-muted mb-0">Create and manage customer discount coupons.</p>
        </div>

        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Coupon
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        @foreach([
            ['Total Coupons', $stats['total'], 'fa-ticket-alt'],
            ['Active', $stats['active'], 'fa-check-circle'],
            ['Inactive', $stats['inactive'], 'fa-pause-circle'],
            ['Expired', $stats['expired'], 'fa-clock']
        ] as $stat)
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ $stat[0] }}</div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h3 class="fw-bold mb-0">{{ $stat[1] }}</h3>
                            <i class="fas {{ $stat[2] }} text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <form method="GET" action="{{ route('admin.coupons.index') }}" class="row g-2 mb-4">

                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search coupon code or name..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach(['active','inactive','expired'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="discount_type" class="form-select">
                        <option value="">All Types</option>
                        @foreach(['percentage','fixed','free_shipping'] as $type)
                            <option value="{{ $type }}" @selected(request('discount_type') === $type)>
                                {{ ucwords(str_replace('_',' ', $type)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary flex-grow-1">Filter</button>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>

            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Coupon</th>
                            <th>Discount</th>
                            <th>Minimum Order</th>
                            <th>Usage</th>
                            <th>Validity</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($coupons as $coupon)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $coupon->code }}</div>
                                <small class="text-muted">{{ $coupon->name }}</small>
                            </td>

                            <td>
                                @if($coupon->discount_type === 'percentage')
                                    {{ number_format((float)$coupon->discount_value, 2) }}%
                                @elseif($coupon->discount_type === 'fixed')
                                    ₹{{ number_format((float)$coupon->discount_value, 2) }}
                                @else
                                    Free Shipping
                                @endif
                            </td>

                            <td>₹{{ number_format((float)$coupon->minimum_order_amount, 2) }}</td>

                            <td>
                                {{ $coupon->usages_count }}
                                /
                                {{ $coupon->usage_limit ?? '∞' }}
                            </td>

                            <td>
                                <small>
                                    {{ $coupon->starts_at?->format('d M Y') ?? 'Anytime' }}
                                    <br>
                                    {{ $coupon->expires_at?->format('d M Y') ?? 'No expiry' }}
                                </small>
                            </td>

                            <td>
                                @php
                                    $expired = $coupon->expires_at && $coupon->expires_at->isPast();
                                @endphp

                                @if($expired)
                                    <span class="badge bg-secondary">Expired</span>
                                @elseif($coupon->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($coupon->status === 'inactive')
                                    <span class="badge bg-warning text-dark">Inactive</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($coupon->status) }}</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <div class="d-inline-flex gap-1">

                                    <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form method="POST"
                                          action="{{ route('admin.coupons.status', $coupon) }}">
                                        @csrf
                                        @method('PATCH')

                                        <button class="btn btn-sm btn-outline-warning"
                                                title="Toggle status"
                                                onclick="return confirm('Change coupon status?')">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                    </form>

                                    <form method="POST"
                                          action="{{ route('admin.coupons.destroy', $coupon) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-outline-danger"
                                                title="Delete"
                                                onclick="return confirm('Delete this coupon?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-ticket-alt fs-2 mb-3 d-block"></i>
                                No coupons found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $coupons->links() }}
            </div>

        </div>
    </div>

</div>
@endsection
