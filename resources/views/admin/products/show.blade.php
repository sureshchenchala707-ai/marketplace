
@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Product Details</h3>
            <p class="text-muted mb-0">Review complete product information.</p>
        </div>

        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Products
        </a>
    </div>

    @php
        $statusColors = [
            'draft' => 'secondary',
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'inactive' => 'dark',
        ];

        $primaryImage = $product->images->firstWhere('is_primary', true)
            ?? $product->images->first();
    @endphp

    <div class="row g-4">

        {{-- Main Product Information --}}
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                        <div>
                            <h4 class="fw-bold mb-2">{{ $product->name }}</h4>
                            <span class="badge text-bg-{{ $statusColors[$product->status] ?? 'secondary' }}">
                                {{ ucfirst($product->status) }}
                            </span>
                            @if($product->is_featured)
                                <span class="badge text-bg-info">Featured</span>
                            @endif
                        </div>

                        <div class="text-lg-end">
                            <h4 class="fw-bold text-primary mb-0">
                                ₹{{ number_format((float) $product->current_price, 2) }}
                            </h4>
                            @if($product->sale_price !== null)
                                <small class="text-muted text-decoration-line-through">
                                    ₹{{ number_format((float) $product->regular_price, 2) }}
                                </small>
                            @endif
                        </div>
                    </div>

                    {{-- Product Images --}}
                    <h6 class="fw-bold mb-3">Product Images</h6>

                    @if($product->images->isNotEmpty())
                        <div class="row g-3 mb-4">
                            @foreach($product->images as $image)
                                <div class="col-6 col-md-3">
                                    <div class="border rounded p-2 h-100">
                                        <img
                                            src="{{ asset('storage/' . $image->image_path) }}"
                                            alt="{{ $image->alt_text ?: $product->name }}"
                                            class="img-fluid rounded w-100"
                                            style="height:150px;object-fit:cover"
                                        >
                                        @if($image->is_primary)
                                            <span class="badge text-bg-primary mt-2">Primary Image</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-light rounded text-center p-5 mb-4">
                            <i class="fas fa-image fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No product images uploaded.</p>
                        </div>
                    @endif

                    <hr>

                    {{-- Description --}}
                    <h6 class="fw-bold mb-3">Product Description</h6>

                    <p class="text-muted">
                        {{ $product->short_description ?: 'No short description available.' }}
                    </p>

                    <div class="text-secondary">
                        {!! nl2br(e($product->description ?: 'No detailed description available.')) !!}
                    </div>
                </div>
            </div>

            {{-- Specifications --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Product Specifications</h5>

                    @if($product->specifications->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <tbody>
                                    @foreach($product->specifications as $attribute)
                                        <tr>
                                            <th class="bg-light" style="width:35%">
                                                {{ $attribute->attribute_name }}
                                            </th>
                                            <td>{{ $attribute->attribute_value }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No specifications added.</p>
                    @endif
                </div>
            </div>

            {{-- Variants --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Product Variants</h5>

                    @if($product->variants->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Variant</th>
                                        <th>SKU</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->variants as $variant)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $variant->variant_name }}</div>
                                                <small class="text-muted">{{ $variant->attributes }}</small>
                                            </td>
                                            <td>{{ $variant->sku }}</td>
                                            <td>
                                                ₹{{ number_format((float) ($variant->sale_price ?? $variant->regular_price), 2) }}
                                            </td>
                                            <td>{{ $variant->stock_quantity }}</td>
                                            <td>
                                                <span class="badge text-bg-{{ $variant->is_active ? 'success' : 'secondary' }}">
                                                    {{ $variant->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No variants available.</p>
                    @endif
                </div>
            </div>

            {{-- CUSTOMER REVIEWS --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Customer Reviews</h5>

            <span class="badge text-bg-primary">
                {{ $product->reviews->count() }} Total
            </span>
        </div>

        @forelse($product->reviews as $review)

            <div class="border rounded p-3 mb-3">

                <div class="d-flex justify-content-between flex-wrap gap-2">

                    <div>
                        <strong>
                            {{ $review->user?->name ?? 'Customer' }}
                        </strong>

                        <div class="text-warning mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-muted' }}"></i>
                            @endfor
                        </div>
                    </div>

                    <span class="badge text-bg-{{
                        $review->status === 'approved'
                            ? 'success'
                            : ($review->status === 'rejected' ? 'danger' : 'warning')
                    }}">
                        {{ ucfirst($review->status) }}
                    </span>

                </div>

                @if($review->title)
                    <h6 class="fw-semibold mt-3 mb-1">
                        {{ $review->title }}
                    </h6>
                @endif

                <p class="text-muted mb-2">
                    {{ $review->review }}
                </p>

                @if($review->is_verified_purchase)
                    <small class="text-success d-block mb-2">
                        <i class="fas fa-check-circle me-1"></i>
                        Verified Purchase
                    </small>
                @endif

                <small class="text-muted d-block mb-3">
                    Submitted: {{ $review->created_at->format('d M Y, h:i A') }}
                </small>

                @if($review->status === 'pending')

                    <div class="d-flex gap-2">

                        <form action="{{ route('admin.reviews.approve', $review) }}"
                              method="POST"
                              onsubmit="return confirm('Approve this review?')">
                            @csrf

                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-check me-1"></i>
                                Approve
                            </button>
                        </form>

                        <form action="{{ route('admin.reviews.reject', $review) }}"
                              method="POST"
                              onsubmit="return confirm('Reject this review?')">
                            @csrf

                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-times me-1"></i>
                                Reject
                            </button>
                        </form>

                    </div>

                @endif

            </div>

        @empty

            <p class="text-muted mb-0">
                No customer reviews yet.
            </p>

        @endforelse

    </div>
</div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            {{-- Pricing and Inventory --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Pricing & Inventory</h5>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Regular Price</span>
                        <strong>₹{{ number_format((float) $product->regular_price, 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Sale Price</span>
                        <strong>
                            {{ $product->sale_price !== null ? '₹' . number_format((float) $product->sale_price, 2) : 'Not set' }}
                        </strong>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Stock Quantity</span>
                        <strong>{{ $product->stock_quantity }}</strong>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Low Stock Alert</span>
                        <strong>{{ $product->low_stock_threshold }}</strong>
                    </div>

                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Stock Status</span>
                        @if($product->stock_quantity <= 0)
                            <span class="badge text-bg-danger">Out of Stock</span>
                        @elseif($product->stock_quantity <= $product->low_stock_threshold)
                            <span class="badge text-bg-warning">Low Stock</span>
                        @else
                            <span class="badge text-bg-success">In Stock</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Vendor Information --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Vendor Information</h5>

                    <h6 class="fw-semibold">{{ $product->vendor?->shop_name ?? 'Vendor unavailable' }}</h6>

                    <p class="text-muted small mb-2">
                        {{ $product->vendor?->user?->name ?? 'N/A' }}
                    </p>

                    <p class="small mb-2">
                        <i class="fas fa-envelope text-muted me-2"></i>
                        {{ $product->vendor?->business_email ?: ($product->vendor?->user?->email ?? 'N/A') }}
                    </p>

                    <p class="small mb-2">
                        <i class="fas fa-phone text-muted me-2"></i>
                        {{ $product->vendor?->business_phone ?? 'N/A' }}
                    </p>

                    <p class="small mb-0">
                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                        {{ collect([$product->vendor?->city, $product->vendor?->state, $product->vendor?->pincode])->filter()->implode(', ') ?: 'N/A' }}
                    </p>
                </div>
            </div>

            {{-- Product Information --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">General Information</h5>

                    <div class="mb-3">
                        <small class="text-muted d-block">SKU</small>
                        <strong>{{ $product->sku }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Category</small>
                        <strong>{{ $product->category?->name ?? 'N/A' }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Brand</small>
                        <strong>{{ $product->brand ?: 'N/A' }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Weight</small>
                        <strong>
                            {{ $product->weight !== null ? $product->weight . ' ' . ($product->weight_unit ?? '') : 'N/A' }}
                        </strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Created On</small>
                        <strong>{{ $product->created_at?->format('d M Y, h:i A') }}</strong>
                    </div>

                    <div class="mb-0">
                        <small class="text-muted d-block">Last Updated</small>
                        <strong>{{ $product->updated_at?->format('d M Y, h:i A') }}</strong>
                    </div>
                </div>
            </div>

{{-- Product Approval Actions --}}
@if($product->status === 'pending')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Product Approval</h5>

            <p class="text-muted small">
                Review this product before making an approval decision.
            </p>

            <form action="{{ route('admin.products.approve', $product) }}"
                  method="POST"
                  class="mb-3"
                  onsubmit="return confirm('Are you sure you want to approve this product?')">
                @csrf

                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-check-circle me-2"></i>
                    Approve Product
                </button>
            </form>

            <button type="button"
                    class="btn btn-outline-danger w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#rejectProductModal">
                <i class="fas fa-times-circle me-2"></i>
                Reject Product
            </button>
        </div>
    </div>

    {{-- Rejection Modal --}}
    <div class="modal fade" id="rejectProductModal" tabindex="-1"
         aria-labelledby="rejectProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.products.reject', $product) }}"
                      method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="rejectProductModalLabel">
                            Reject Product
                        </h5>
                        <button type="button" class="btn-close"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-muted">
                            Please provide a reason for rejecting this product.
                        </p>

                        <label for="admin_remarks" class="form-label fw-semibold">
                            Rejection Reason <span class="text-danger">*</span>
                        </label>

                        <textarea
                            name="admin_remarks"
                            id="admin_remarks"
                            class="form-control"
                            rows="4"
                            maxlength="1000"
                            required
                            placeholder="Explain why this product is rejected..."
                        >{{ old('admin_remarks') }}</textarea>

                        @error('admin_remarks')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                                data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times me-2"></i>Confirm Rejection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif


            {{-- Admin Remarks --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Admin Remarks</h5>

                    @if($product->admin_remarks)
                        <p class="text-muted mb-0">{{ $product->admin_remarks }}</p>
                    @else
                        <p class="text-muted mb-0">No admin remarks available.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Success Message --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}

        <button type="button" class="btn-close"
                data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Error Message --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}

        <button type="button" class="btn-close"
                data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Validation Errors --}}
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <strong>Please correct the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>

        <button type="button" class="btn-close"
                data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@endsection
