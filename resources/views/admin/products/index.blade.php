
@extends('layouts.app')

@section('title', 'Product Management')

@section('content')
<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1">Product Management</h3>
            <p class="text-muted mb-0">Manage, review and monitor marketplace products.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Dashboard
        </a>
    </div>

    {{-- Statistics --}}
    <div class="row g-3 mb-4">
        @php
            $statistics = [
                ['label' => 'Total Products', 'value' => $counts['total'], 'icon' => 'fa-box', 'color' => '#4f46e5'],
                ['label' => 'Pending Review', 'value' => $counts['pending'], 'icon' => 'fa-clock', 'color' => '#d97706'],
                ['label' => 'Approved', 'value' => $counts['approved'], 'icon' => 'fa-check-circle', 'color' => '#059669'],
                ['label' => 'Rejected', 'value' => $counts['rejected'], 'icon' => 'fa-times-circle', 'color' => '#dc2626'],
                ['label' => 'Draft', 'value' => $counts['draft'], 'icon' => 'fa-file-alt', 'color' => '#64748b'],
                ['label' => 'Inactive', 'value' => $counts['inactive'], 'icon' => 'fa-ban', 'color' => '#475569'],
            ];
        @endphp

        @foreach($statistics as $stat)
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">{{ $stat['label'] }}</span>
                            <i class="fas {{ $stat['icon'] }}" style="color: {{ $stat['color'] }}"></i>
                        </div>
                        <h3 class="fw-bold mb-0">{{ number_format($stat['value']) }}</h3>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Product Listing --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0">All Products</h5>
        </div>

        <div class="card-body">

            {{-- Filters --}}
            <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3 mb-4">

                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-semibold">Search</label>
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Product, SKU, brand or vendor..."
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach(['draft', 'pending', 'approved', 'rejected', 'inactive'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-semibold">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label small fw-semibold">Vendor</label>
                    <select name="vendor_id" class="form-select">
                        <option value="">All Vendors</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" @selected((string) request('vendor_id') === (string) $vendor->id)>
                                {{ $vendor->shop_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
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
                            <th>Vendor</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end">Action</th>
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

                                <td>
                                    {{ $product->vendor?->shop_name ?? 'N/A' }}
                                    <br>
                                    <small class="text-muted">{{ $product->vendor?->user?->email ?? '' }}</small>
                                </td>

                                <td>{{ $product->category?->name ?? 'N/A' }}</td>

                                <td>
                                    <div class="fw-semibold">₹{{ number_format((float) $product->regular_price, 2) }}</div>
                                    @if($product->sale_price !== null)
                                        <small class="text-success">Sale: ₹{{ number_format((float) $product->sale_price, 2) }}</small>
                                    @endif
                                </td>

                                <td>
                                    <span class="{{ $product->stock_quantity <= $product->low_stock_threshold ? 'text-danger fw-semibold' : '' }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge text-bg-{{ $statusColors[$product->status] ?? 'secondary' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>

                                <td>{{ $product->created_at?->format('d M Y') }}</td>

                                <td class="text-end">
                                    <a href="{{ route('admin.products.show', $product) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="View Product">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-box-open fa-2x text-muted mb-3"></i>
                                    <h6 class="fw-semibold">No products found</h6>
                                    <p class="text-muted mb-0">Products will appear here when vendors add them.</p>
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
