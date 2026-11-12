@extends('layouts.app')

@section('title', 'My Products')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">My Products</h3>
            <p class="text-muted mb-0">Manage your products, pricing and inventory.</p>
        </div>

        <a href="{{ route('vendor.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Product
        </a>
    </div>

    {{-- Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Statistics --}}
    @php
        $statistics = [
            ['label' => 'Total Products', 'value' => $counts['total'], 'icon' => 'fa-box', 'color' => '#4f46e5'],
            ['label' => 'Pending Review', 'value' => $counts['pending'], 'icon' => 'fa-clock', 'color' => '#d97706'],
            ['label' => 'Approved', 'value' => $counts['approved'], 'icon' => 'fa-check-circle', 'color' => '#059669'],
            ['label' => 'Rejected', 'value' => $counts['rejected'], 'icon' => 'fa-times-circle', 'color' => '#dc2626'],
            ['label' => 'Draft', 'value' => $counts['draft'], 'icon' => 'fa-file-alt', 'color' => '#64748b'],
        ];
    @endphp

    <div class="row g-3 mb-4">
        @foreach($statistics as $stat)
            <div class="col-6 col-md-4 col-xl">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">{{ $stat['label'] }}</span>
                            <i class="fas {{ $stat['icon'] }}" style="color:{{ $stat['color'] }}"></i>
                        </div>
                        <h3 class="fw-bold mb-0">{{ number_format($stat['value']) }}</h3>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Products Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0">Product Inventory</h5>
        </div>

        <div class="card-body">

            {{-- Search and Filter --}}
            <form method="GET" action="{{ route('vendor.products.index') }}" class="row g-3 mb-4">

                <div class="col-lg-5 col-md-6">
                    <label class="form-label small fw-semibold">Search Products</label>
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search by product name, SKU or brand..."
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-lg-3 col-md-4">
                    <label class="form-label small fw-semibold">Product Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach(['draft', 'pending', 'approved', 'rejected', 'inactive'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-4 col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                    <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($products as $product)
                            @php
                                $statusColors = [
                                    'draft' => 'secondary',
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'inactive' => 'dark',
                                ];
                            @endphp

                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($product->images->first())
                                            <img
                                                src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                                                alt="{{ $product->name }}"
                                                width="48"
                                                height="48"
                                                class="rounded border"
                                                style="object-fit:cover"
                                            >
                                        @else
                                            <div class="bg-light rounded border d-flex align-items-center justify-content-center"
                                                 style="width:48px;height:48px">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif

                                        <div>
                                            <div class="fw-semibold">{{ $product->name }}</div>
                                            <small class="text-muted">{{ $product->brand ?: 'No brand' }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>{{ $product->sku }}</td>

                                <td>{{ $product->category?->name ?? 'N/A' }}</td>

                                <td>
                                    <strong>₹{{ number_format((float) $product->current_price, 2) }}</strong>
                                    @if($product->sale_price !== null)
                                        <br>
                                        <small class="text-muted text-decoration-line-through">
                                            ₹{{ number_format((float) $product->regular_price, 2) }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    <span class="{{ $product->stock_quantity <= $product->low_stock_threshold ? 'text-danger fw-semibold' : '' }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                    @if($product->stock_quantity <= $product->low_stock_threshold)
                                        <br><small class="text-danger">Low stock</small>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge text-bg-{{ $statusColors[$product->status] ?? 'secondary' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                    @if($product->status === 'rejected' && $product->admin_remarks)
                                        <br>
                                        <small class="text-danger" title="{{ $product->admin_remarks }}">
                                            View rejection reason
                                        </small>
                                    @endif
                                </td>

                                <td>{{ $product->created_at?->format('d M Y') }}</td>

                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('vendor.products.show', $product) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('vendor.products.edit', $product) }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('vendor.products.destroy', $product) }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this product?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-box-open fa-2x text-muted mb-3"></i>
                                    <h6 class="fw-semibold">No products found</h6>
                                    <p class="text-muted mb-3">
                                        You haven't added any products yet.
                                    </p>
                                    <a href="{{ route('vendor.products.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-2"></i>Add Your First Product
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                <small class="text-muted">
                    Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }}
                    of {{ $products->total() }} products
                </small>

                {{ $products->links() }}
            </div>

        </div>
    </div>
</div>
@endsection
