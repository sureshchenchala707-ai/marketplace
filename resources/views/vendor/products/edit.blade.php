
@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1">Edit Product</h3>
            <p class="text-muted mb-0">Update your product information.</p>
        </div>
        <a href="{{ route('vendor.products.show', $product) }}"
           class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Product
        </a>
    </div>

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

    <form action="{{ route('vendor.products.update', $product) }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-semibold">Basic Information</h5>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Product Name *</label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $product->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Category *</label>
                                <select name="category_id"
                                        class="form-select @error('category_id') is-invalid @enderror"
                                        required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->parent_id ? '— ' : '' }}{{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Brand</label>
                                <input type="text" name="brand" class="form-control"
                                       value="{{ old('brand', $product->brand) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Short Description</label>
                            <textarea name="short_description" rows="2" class="form-control">{{ old('short_description', $product->short_description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Description</label>
                            <textarea name="description" rows="6" class="form-control">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-semibold">Pricing & Inventory</h5>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Regular Price (₹) *</label>
                                <input type="number" step="0.01" min="0.01"
                                       name="regular_price" class="form-control"
                                       value="{{ old('regular_price', $product->regular_price) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Sale Price (₹)</label>
                                <input type="number" step="0.01" min="0.01"
                                       name="sale_price" class="form-control"
                                       value="{{ old('sale_price', $product->sale_price) }}">
                                <small class="text-muted">Must be lower than regular price.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Stock Quantity *</label>
                                <input type="number" min="0" name="stock_quantity"
                                       class="form-control"
                                       value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Low Stock Alert</label>
                                <input type="number" min="0" name="low_stock_threshold"
                                       class="form-control"
                                       value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Weight</label>
                                <input type="number" step="0.001" min="0.001"
                                       name="weight" class="form-control"
                                       value="{{ old('weight', $product->weight) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Weight Unit</label>
                                <select name="weight_unit" class="form-select">
                                    <option value="">Select Unit</option>
                                    @foreach(['kg'=>'Kilogram (kg)', 'g'=>'Gram (g)', 'lb'=>'Pound (lb)'] as $value=>$label)
                                        <option value="{{ $value }}"
                                            {{ old('weight_unit', $product->weight_unit) === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold">SKU</label>
                            <input type="text" class="form-control"
                                   value="{{ $product->sku }}" readonly>
                            <small class="text-muted">SKU cannot be changed.</small>
                        </div>
                    </div>
                </div>


<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="mb-0 fw-semibold">Product Variants</h5>
            <small class="text-muted">Add different sizes, colors or models.</small>
        </div>

        <button type="button" class="btn btn-sm btn-outline-primary" id="addVariantBtn">
            <i class="fa-solid fa-plus me-1"></i> Add Variant
        </button>
    </div>

    <div class="card-body">
        <div id="variantsContainer">

            @foreach($product->variants as $index => $variant)
                <div class="variant-row border rounded p-3 mb-3">

                                    <input type="hidden"
                        name="variants[{{ $index }}][id]"
                        value="{{ $variant->id }}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <strong>Variant #{{ $index + 1 }}</strong>

                      <button type="button"
                            class="btn btn-danger btn-sm delete-variant-btn"
                            data-variant-id="{{ $variant->id }}"
                            data-delete-url="{{ route('vendor.products.variants.destroy', [
                                'product' => $product->id,
                                'variant' => $variant->id
                            ]) }}">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Variant Name *</label>
                            <input type="text"
                                   name="variants[{{ $index }}][variant_name]"
                                   class="form-control"
                                   value="{{ $variant->variant_name }}"
                                   maxlength="150"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Variant SKU</label>
                            <input type="text"
                                   name="variants[{{ $index }}][sku]"
                                   class="form-control"
                                   value="{{ $variant->sku }}"
                                   maxlength="100"
                                   required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Attributes</label>
                            <input type="text"
                                   name="variants[{{ $index }}][attributes]"
                                   class="form-control"
                                   value="{{ $variant->attributes }}"
                                   maxlength="1000"
                                   placeholder="Example: Color: Black, Size: Large">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Regular Price (₹) *</label>
                            <input type="number" step="0.01" min="0.01"
                                   name="variants[{{ $index }}][regular_price]"
                                   class="form-control"
                                   value="{{ $variant->regular_price }}"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Sale Price (₹)</label>
                            <input type="number" step="0.01" min="0.01"
                                   name="variants[{{ $index }}][sale_price]"
                                   class="form-control"
                                   value="{{ $variant->sale_price }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Stock Quantity *</label>
                            <input type="number" min="0"
                                   name="variants[{{ $index }}][stock_quantity]"
                                   class="form-control"
                                   value="{{ $variant->stock_quantity }}"
                                   required>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox"
                                       class="form-check-input"
                                       name="variants[{{ $index }}][is_active]"
                                       value="1"
                                       id="variant_active_{{ $index }}"
                                       {{ $variant->is_active ? 'checked' : '' }}>
                                <label class="form-check-label"
                                       for="variant_active_{{ $index }}">
                                    Active Variant
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

        <div id="noVariantsMessage"
             class="text-center text-muted py-3"
             style="{{ $product->variants->count() ? 'display:none;' : '' }}">
            No variants added yet. Click "Add Variant" to create one.
        </div>

        <small class="text-muted">
            Each variant needs a unique SKU. You can add up to 20 variants per product.
        </small>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('variantsContainer');
    const addButton = document.getElementById('addVariantBtn');
    const message = document.getElementById('noVariantsMessage');

    let nextIndex = {{ $product->variants->count() }};

    function updateMessage() {
        message.style.display =
            container.querySelectorAll('.variant-row').length === 0
                ? 'block'
                : 'none';
    }

    addButton.addEventListener('click', function () {
        if (container.querySelectorAll('.variant-row').length >= 20) {
            alert('Maximum 20 variants are allowed per product.');
            return;
        }

        const index = nextIndex++;

        const row = document.createElement('div');
        row.className = 'variant-row border rounded p-3 mb-3';

        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <strong>New Variant</strong>
                <button type="button" class="btn btn-sm btn-outline-danger remove-new-variant">
                    <i class="fa-solid fa-trash me-1"></i> Remove
                </button>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Variant Name *</label>
                    <input type="text" name="variants[${index}][variant_name]"
                           class="form-control" maxlength="150"
                           placeholder="Example: Black - Large" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Variant SKU *</label>
                    <input type="text" name="variants[${index}][sku]"
                           class="form-control" maxlength="100"
                           placeholder="Enter a unique SKU" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Attributes</label>
                    <input type="text" name="variants[${index}][attributes]"
                           class="form-control" maxlength="1000"
                           placeholder="Example: Color: Black, Size: Large">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Regular Price (₹) *</label>
                    <input type="number" step="0.01" min="0.01"
                           name="variants[${index}][regular_price]"
                           class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Sale Price (₹)</label>
                    <input type="number" step="0.01" min="0.01"
                           name="variants[${index}][sale_price]"
                           class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Stock Quantity *</label>
                    <input type="number" min="0"
                           name="variants[${index}][stock_quantity]"
                           class="form-control" value="0" required>
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox"
                               name="variants[${index}][is_active]"
                               value="1" class="form-check-input"
                               id="variant_active_${index}" checked>
                        <label class="form-check-label"
                               for="variant_active_${index}">
                            Active Variant
                        </label>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(row);
        updateMessage();
    });

    container.addEventListener('click', function (event) {
        const button = event.target.closest('.remove-new-variant');

        if (button) {
            button.closest('.variant-row').remove();
            updateMessage();
        }
    });

    updateMessage();
});
</script>

       <!-- //specifications          -->
        {{-- PRODUCT SPECIFICATIONS --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">
                    <i class="fas fa-list-alt text-primary me-2"></i>
                    Product Specifications
                </h5>
                <small class="text-muted">
                    Add product details such as Brand, Color, Warranty, etc.
                </small>
            </div>

            <button type="button"
                    class="btn btn-primary btn-sm"
                    id="addSpecificationBtn">
                <i class="fas fa-plus me-1"></i> Add Specification
            </button>
        </div>
    </div>

    <div class="card-body">

        <div id="specificationsContainer">

            @foreach($product->specifications as $index => $specification)

                <div class="specification-row border rounded p-3 mb-3">

                    <input type="hidden"
                           name="specifications[{{ $index }}][id]"
                           value="{{ $specification->id }}">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <strong>Specification #{{ $index + 1 }}</strong>

                        <button type="button"
                                class="btn btn-outline-danger btn-sm remove-specification-btn">
                            <i class="fas fa-trash me-1"></i> Remove
                        </button>
                    </div>

                    <div class="row g-3">

                        <div class="col-md-5">
                            <label class="form-label">Specification Name *</label>

                            <input type="text"
                                   name="specifications[{{ $index }}][attribute_name]"
                                   class="form-control"
                                   value="{{ $specification->attribute_name }}"
                                   maxlength="150"
                                   placeholder="Example: Brand"
                                   required>
                        </div>

                        <div class="col-md-7">
                            <label class="form-label">Specification Value *</label>

                            <input type="text"
                                   name="specifications[{{ $index }}][attribute_value]"
                                   class="form-control"
                                   value="{{ $specification->attribute_value }}"
                                   placeholder="Example: Sony"
                                   required>
                        </div>

                    </div>
                </div>

            @endforeach

        </div>

        <div id="noSpecificationsMessage"
             class="text-center text-muted py-4"
             style="{{ $product->specifications->count() ? 'display:none;' : '' }}">

            <i class="fas fa-clipboard-list fa-2x mb-2"></i>

            <p class="mb-0">
                No specifications added yet.
            </p>

            <small>Click Add Specification to get started.</small>
        </div>

    </div>
</div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-semibold">Existing Product Images</h5>
                    </div>

                    <div class="card-body">
                        @if($product->images->count())
                            <div class="row g-3">
                                @foreach($product->images as $image)
                                    <div class="col-6 col-md-4 col-xl-3">
                                        <div class="border rounded p-2 h-100">
                                            <img src="{{ asset('storage/'.$image->image_path) }}"
                                                 class="img-fluid rounded mb-2"
                                                 style="width:100%;height:130px;object-fit:cover;">

                                            <div class="form-check">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       name="remove_images[]"
                                                       value="{{ $image->id }}"
                                                       id="remove_image_{{ $image->id }}">
                                                <label class="form-check-label small"
                                                       for="remove_image_{{ $image->id }}">
                                                    Remove image
                                                </label>
                                            </div>

                                            @if($image->is_primary)
                                                <span class="badge bg-success mt-2">Primary Image</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No existing images.</p>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-semibold">Upload New Images</h5>
                    </div>

                    <div class="card-body">
                        <input type="file" name="images[]" multiple
                               accept=".jpg,.jpeg,.png,.webp"
                               class="form-control">
                        <small class="text-muted">
                            JPG, PNG, WEBP. Maximum 2 MB per image.
                            Maximum 5 images in total.
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Product Status</h5>

                        <span class="badge bg-warning text-dark fs-6">
                            {{ ucfirst($product->status) }}
                        </span>

                        <p class="text-muted small mt-3 mb-0">
                            Saving changes will submit this product for admin re-approval.
                        </p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-save me-1"></i> Update Product
                        </button>

                        <a href="{{ route('vendor.products.index') }}"
                           class="btn btn-outline-secondary w-100 mt-2">
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

    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded.');
        return;
    }

    $(document).on('click', '.delete-variant-btn', function () {

        const button = $(this);
        const variantRow = button.closest('.variant-row');
        const deleteUrl = button.data('delete-url');

        if (!confirm('Are you sure you want to permanently delete this variant?')) {
            return;
        }

        button.prop('disabled', true)
              .html('<i class="fas fa-spinner fa-spin"></i> Deleting...');

        $.ajax({
            url: deleteUrl,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },

            success: function (response) {
                variantRow.fadeOut(250, function () {
                    $(this).remove();
                });

                alert(response.message);
            },

            error: function (xhr) {
                button.prop('disabled', false)
                      .html('<i class="fas fa-trash"></i> Delete');

                let message = 'Unable to delete variant. Please try again.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                alert(message);
            }
        });
    });

});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('specificationsContainer');
    const addButton = document.getElementById('addSpecificationBtn');
    const emptyMessage = document.getElementById('noSpecificationsMessage');

    if (!container || !addButton) {
        return;
    }

    let specificationIndex = {{ $product->specifications->count() }};

    function updateSpecificationNumbers() {

        const rows = container.querySelectorAll('.specification-row');

        rows.forEach(function (row, index) {
            const heading = row.querySelector('strong');

            if (heading) {
                heading.textContent = 'Specification #' + (index + 1);
            }
        });

        if (emptyMessage) {
            emptyMessage.style.display = rows.length === 0 ? 'block' : 'none';
        }
    }

    addButton.addEventListener('click', function () {

        const row = document.createElement('div');

        row.className = 'specification-row border rounded p-3 mb-3';

        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <strong>New Specification</strong>

                <button type="button"
                        class="btn btn-outline-danger btn-sm remove-specification-btn">
                    <i class="fas fa-trash me-1"></i> Remove
                </button>
            </div>

            <div class="row g-3">

                <div class="col-md-5">
                    <label class="form-label">Specification Name *</label>

                    <input type="text"
                           name="specifications[${specificationIndex}][attribute_name]"
                           class="form-control"
                           maxlength="150"
                           placeholder="Example: Brand"
                           required>
                </div>

                <div class="col-md-7">
                    <label class="form-label">Specification Value *</label>

                    <input type="text"
                           name="specifications[${specificationIndex}][attribute_value]"
                           class="form-control"
                           placeholder="Example: Sony"
                           required>
                </div>

            </div>
        `;

        container.appendChild(row);

        specificationIndex++;

        updateSpecificationNumbers();
    });

    container.addEventListener('click', function (event) {

        const removeButton = event.target.closest('.remove-specification-btn');

        if (!removeButton) {
            return;
        }

        const row = removeButton.closest('.specification-row');

        if (row) {
            row.remove();
            updateSpecificationNumbers();
        }
    });

    updateSpecificationNumbers();

});
</script>
@endpush