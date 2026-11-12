
@extends('layouts.app')

@section('title', 'Add New Product')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Add New Product</h3>
            <p class="text-muted mb-0">Enter your product information for admin review.</p>
        </div>

        <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Products
        </a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('vendor.products.store') }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf

        <div class="row g-4">

            {{-- Main Form --}}
            <div class="col-lg-8">

                {{-- Basic Information --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Basic Information</h5>
                    </div>

                    <div class="card-body p-4">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Product Name <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}"
                                maxlength="200"
                                required
                                placeholder="Enter product name"
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Category <span class="text-danger">*</span>
                                </label>
                                <select
                                    name="category_id"
                                    class="form-select @error('category_id') is-invalid @enderror"
                                    required
                                >
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            @selected(old('category_id') == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Brand</label>
                                <input
                                    type="text"
                                    name="brand"
                                    class="form-control @error('brand') is-invalid @enderror"
                                    value="{{ old('brand') }}"
                                    maxlength="150"
                                    placeholder="Enter brand name"
                                >
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-semibold">
                                Short Description
                            </label>
                            <textarea
                                name="short_description"
                                class="form-control @error('short_description') is-invalid @enderror"
                                rows="3"
                                maxlength="1000"
                                placeholder="Brief product description"
                            >{{ old('short_description') }}</textarea>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-semibold">
                                Full Description
                            </label>
                            <textarea
                                name="description"
                                class="form-control @error('description') is-invalid @enderror"
                                rows="6"
                                placeholder="Enter complete product description"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Pricing and Inventory --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Pricing & Inventory</h5>
                    </div>

                    <div class="card-body p-4">

                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Regular Price (₹) <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="number"
                                    name="regular_price"
                                    class="form-control @error('regular_price') is-invalid @enderror"
                                    value="{{ old('regular_price') }}"
                                    min="0.01"
                                    max="9999999999.99"
                                    step="0.01"
                                    required
                                >
                                @error('regular_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Sale Price (₹)</label>
                                <input
                                    type="number"
                                    name="sale_price"
                                    class="form-control @error('sale_price') is-invalid @enderror"
                                    value="{{ old('sale_price') }}"
                                    min="0.01"
                                    max="9999999999.99"
                                    step="0.01"
                                >
                                @error('sale_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Stock Quantity <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="number"
                                    name="stock_quantity"
                                    class="form-control @error('stock_quantity') is-invalid @enderror"
                                    value="{{ old('stock_quantity', 0) }}"
                                    min="0"
                                    required
                                >
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Low Stock Alert</label>
                                <input
                                    type="number"
                                    name="low_stock_threshold"
                                    class="form-control @error('low_stock_threshold') is-invalid @enderror"
                                    value="{{ old('low_stock_threshold', 5) }}"
                                    min="0"
                                >
                                @error('low_stock_threshold')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">SKU</label>
                                <input
                                    type="text"
                                    name="sku"
                                    class="form-control @error('sku') is-invalid @enderror"
                                    value="{{ old('sku') }}"
                                    maxlength="100"
                                    placeholder="Leave blank for automatic SKU"
                                >
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">You can leave this blank to generate a unique SKU automatically.</small>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Product Images --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Product Images</h5>
                    </div>

                    <div class="card-body p-4">

                        <label class="form-label fw-semibold">
                            Upload Images
                        </label>

                        <input
                            type="file"
                            name="images[]"
                            id="productImages"
                            class="form-control @error('images') is-invalid @enderror"
                            accept="image/jpeg,image/png,image/webp"
                            multiple
                        >

                        <small class="text-muted">
                            JPG, PNG or WEBP. Maximum 2 MB per image. First image will be the primary image.
                        </small>

                        @error('images')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        @error('images.*')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        <div id="imagePreview" class="row g-3 mt-2"></div>

                    </div>
                </div>

                {{-- Shipping Information --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Shipping Information</h5>
                    </div>

                    <div class="card-body p-4">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Weight</label>
                                <input
                                    type="number"
                                    name="weight"
                                    class="form-control @error('weight') is-invalid @enderror"
                                    value="{{ old('weight') }}"
                                    min="0.001"
                                    max="9999999.999"
                                    step="0.001"
                                >
                                @error('weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Weight Unit</label>
                                <select name="weight_unit" class="form-select">
                                    <option value="">Select Unit</option>
                                    <option value="kg" @selected(old('weight_unit') === 'kg')>Kilogram (kg)</option>
                                    <option value="g" @selected(old('weight_unit') === 'g')>Gram (g)</option>
                                    <option value="lb" @selected(old('weight_unit') === 'lb')>Pound (lb)</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Submission Information</h5>
                    </div>

                    <div class="card-body p-4">

                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-2"></i>
                            New products will be submitted for admin approval. They will not be publicly visible until approved.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Product Status</label>
                            <div class="form-control bg-light">
                                <span class="badge text-bg-warning">Pending Review</span>
                            </div>
                            <small class="text-muted">Products are submitted for approval automatically.</small>
                        </div>

                        <hr>

                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-paper-plane me-2"></i>Submit Product
                        </button>

                        <a href="{{ route('vendor.products.index') }}"
                           class="btn btn-outline-secondary w-100">
                            Cancel
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('productImages');
    const preview = document.getElementById('imagePreview');

    if (!input || !preview) return;

    input.addEventListener('change', function () {
        preview.innerHTML = '';

        Array.from(this.files).forEach(function (file) {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();

            reader.onload = function (event) {
                const column = document.createElement('div');
                column.className = 'col-4 col-md-3';

                const image = document.createElement('img');
                image.src = event.target.result;
                image.alt = 'Product image preview';
                image.className = 'img-fluid rounded border';
                image.style.height = '100px';
                image.style.width = '100%';
                image.style.objectFit = 'cover';

                column.appendChild(image);
                preview.appendChild(column);
            };

            reader.readAsDataURL(file);
        });
    });
});
</script>
@endpush
