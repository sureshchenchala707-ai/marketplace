
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Offer Management</h3>
            <p class="text-muted mb-0">Manage marketplace promotional offers.</p>
        </div>
        <a href="{{ route('admin.offers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Create Offer
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-3 mb-4">
        @foreach([
            ['Total Offers', $stats['total'], 'fa-tags'],
            ['Active', $stats['active'], 'fa-check-circle'],
            ['Inactive', $stats['inactive'], 'fa-pause-circle'],
            ['Expired', $stats['expired'], 'fa-clock']
        ] as [$label, $count, $icon])
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">{{ $label }}</div>
                        <h3 class="mb-0">{{ $count }}</h3>
                        <i class="fas {{ $icon }} text-primary"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search offer name..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <select name="offer_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="percentage" @selected(request('offer_type') === 'percentage')>Percentage</option>
                        <option value="fixed" @selected(request('offer_type') === 'fixed')>Fixed</option>
                        <option value="free_shipping" @selected(request('offer_type') === 'free_shipping')>Free Shipping</option>
                        <option value="bogo" @selected(request('offer_type') === 'bogo')>BOGO</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                        <option value="expired" @selected(request('status') === 'expired')>Expired</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.offers.index') }}" class="btn btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Offer</th>
                        <th>Type</th>
                        <th>Discount</th>
                        <th>Priority</th>
                        <th>Targeting</th>
                        <th>Validity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($offers as $offer)
                    <tr>
                        <td>
                            <strong>{{ $offer->name }}</strong>
                            <div class="small text-muted">{{ $offer->slug }}</div>
                        </td>

                        <td>{{ ucfirst(str_replace('_', ' ', $offer->offer_type)) }}</td>

                        <td>
                            @if($offer->offer_type === 'percentage')
                                {{ $offer->discount_value }}%
                            @elseif($offer->offer_type === 'fixed')
                                ₹{{ number_format((float)$offer->discount_value, 2) }}
                            @elseif($offer->offer_type === 'bogo')
                                Buy {{ $offer->buy_quantity }} Get {{ $offer->get_quantity }}
                            @else
                                Free Shipping
                            @endif
                        </td>

                        <td>{{ $offer->priority }}</td>

                        <td class="small">
                            {{ $offer->products_count }} products<br>
                            {{ $offer->categories_count }} categories<br>
                            {{ $offer->vendors_count }} vendors
                        </td>

                        <td class="small">
                            {{ $offer->starts_at?->format('d M Y') ?? 'Anytime' }}
                            <br>
                            {{ $offer->expires_at?->format('d M Y') ?? 'No expiry' }}
                        </td>

                        <td>
                            <span class="badge {{ $offer->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($offer->status) }}
                            </span>
                        </td>

                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.offers.edit', $offer) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('admin.offers.status', $offer) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-outline-warning" title="Toggle status">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                </form>

                                <form action="{{ route('admin.offers.destroy', $offer) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this offer?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            No offers found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-body">
            {{ $offers->links() }}
        </div>
    </div>

</div>
@endsection
