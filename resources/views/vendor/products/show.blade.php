@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1">Product Details</h3>
            <p class="text-muted mb-0">View your product information and approval status.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('vendor.products.index') }}"
               class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>

            <a href="{{ route('vendor.products.edit', $product) }}"
               class="btn btn-primary">
                <i class="fa-solid fa-pen me-1"></i> Edit Product
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $statusColors = [
            'draft' => 'secondary',
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'inactive' => 'dark',
        ];
    @endphp

    <div class="row g-4">

        {{-- Product Images --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">Product Images</h5>
                </div>

                <div class="card-body">
                    @if($product->images->count())
                        @php
                            $primaryImage = $product->images->firstWhere('is_primary', true)
                                ?? $product->images->first();
                        @endphp

                        <div class="text-center mb-3">
                            <img
                                id="mainProductImage"
                                src="{{ asset('storage/'.$primaryImage->image_path) }}"
                                alt="{{ $product->name }}"
                                class="img-fluid rounded"
                                style="max-height:350px; object-fit:contain;"
                            >
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            @foreach($product->images as $image)
                                <img
                                    src="{{ asset('storage/'.$image->image_path) }}"
                                    alt="{{ $image->alt_text ?? $product->name }}"
                                    onclick="document.getElementById('mainProductImage').src=this.src"
                                    class="rounded border p-1"
                                    style="width:75px;height:75px;object-fit:cover;cursor:pointer;"
                                >
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 bg-light rounded">
                            <i class="fa-solid fa-image fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No product images uploaded.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Product Summary --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                        <h4 class="fw-bold">{{ $product->name }}</h4>

                        <span class="badge bg-{{ $statusColors[$product->status] ?? 'secondary' }} fs-6">
                            {{ ucfirst($product->status) }}
                        </span>
                    </div>

                    <p class="text-muted">
                        SKU: {{ $product->sku }}
                    </p>

                    <div class="mb-3">
                        <span class="fs-3 fw-bold text-primary">
                            ₹{{ number_format((float)$product->current_price, 2) }}
                        </span>

                        @if($product->sale_price)
                            <span class="text-muted text-decoration-line-through ms-2">
                                ₹{{ number_format((float)$product->regular_price, 2) }}
                            </span>
                        @endif
                    </div>

                    <hr>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Category</small>
                            <strong>{{ $product->category->name ?? 'N/A' }}</strong>
                        </div>

                        <div class="col-sm-6">
                            <small class="text-muted d-block">Brand</small>
                            <strong>{{ $product->brand ?: 'N/A' }}</strong>
                        </div>

                        <div class="col-sm-6">
                            <small class="text-muted d-block">Available Stock</small>
                            <strong>{{ $product->stock_quantity }} units</strong>
                        </div>

                        <div class="col-sm-6">
                            <small class="text-muted d-block">Low Stock Alert</small>
                            <strong>{{ $product->low_stock_threshold }} units</strong>
                        </div>

                        <div class="col-sm-6">
                            <small class="text-muted d-block">Weight</small>
                            <strong>
                                {{ $product->weight ? $product->weight.' '.($product->weight_unit ?? '') : 'N/A' }}
                            </strong>
                        </div>

                        <div class="col-sm-6">
                            <small class="text-muted d-block">Created On</small>
                            <strong>{{ $product->created_at->format('d M Y, h:i A') }}</strong>
                        </div>
                    </div>

                    <hr>

                    <h6 class="fw-bold">Short Description</h6>
                    <p class="text-muted">
                        {{ $product->short_description ?: 'No short description available.' }}
                    </p>

                    <h6 class="fw-bold mt-3">Full Description</h6>
                    <div class="text-muted">
                        {!! nl2br(e($product->description ?: 'No description available.')) !!}
                    </div>

                </div>
            </div>
        </div>

        {{-- Approval Information --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Admin Review & Approval</h5>

                    @if($product->status === 'pending')
                        <div class="alert alert-warning mb-0">
                            <i class="fa-solid fa-clock me-2"></i>
                            Your product is waiting for admin approval.
                        </div>
                    @elseif($product->status === 'approved')
                        <div class="alert alert-success mb-0">
                            <i class="fa-solid fa-circle-check me-2"></i>
                            Your product has been approved by the admin.
                        </div>
                    @elseif($product->status === 'rejected')
                        <div class="alert alert-danger mb-0">
                            <i class="fa-solid fa-circle-xmark me-2"></i>
                            Your product was rejected. Please review the admin remarks below.
                        </div>
                    @elseif($product->status === 'draft')
                        <div class="alert alert-secondary mb-0">
                            This product is currently saved as a draft.
                        </div>
                    @elseif($product->status === 'inactive')
                        <div class="alert alert-dark mb-0">
                            This product is currently inactive.
                        </div>
                    @endif

                    @if($product->admin_remarks)
                        <div class="mt-3">
                            <strong>Admin Remarks:</strong>
                            <p class="text-muted mb-0 mt-1">
                                {{ $product->admin_remarks }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Specifications --}}
        @if($product->specifications->count())
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Product Specifications</h5>

                        @foreach($product->specifications as $specification)
                            <div class="d-flex justify-content-between border-bottom py-2 gap-3">
                                <strong>{{ $specification->attribute_name }}</strong>
                                <span class="text-muted text-end">
                                    {{ $specification->attribute_value }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Variants --}}
        @if($product->variants->count())
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Product Variants</h5>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Variant</th>
                                        <th>SKU</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($product->variants as $variant)
                                        <tr>
                                            <td>{{ $variant->variant_name }}</td>
                                            <td>{{ $variant->sku }}</td>
                                            <td>
                                                ₹{{ number_format((float)($variant->sale_price ?? $variant->regular_price), 2) }}
                                            </td>
                                            <td>{{ $variant->stock_quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection