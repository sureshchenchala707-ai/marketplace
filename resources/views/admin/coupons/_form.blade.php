
@csrf

@php
    $selectedProducts = old('product_ids', isset($coupon) ? $coupon->products->pluck('id')->toArray() : []);
    $selectedCategories = old('category_ids', isset($coupon) ? $coupon->categories->pluck('id')->toArray() : []);
    $selectedVendors = old('vendor_ids', isset($coupon) ? $coupon->vendors->pluck('id')->toArray() : []);
@endphp

<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label">Coupon Code *</label>
        <input type="text" name="code" class="form-control text-uppercase"
               value="{{ old('code', $coupon->code ?? '') }}" required maxlength="50">
        @error('code') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Coupon Name *</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $coupon->name ?? '') }}" required>
        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="2">{{ old('description', $coupon->description ?? '') }}</textarea>
    </div>

    <div class="col-md-6">
        <label class="form-label">Discount Type *</label>
        <select name="discount_type" id="discountType" class="form-select" required>
            @foreach(['percentage'=>'Percentage','fixed'=>'Fixed Amount','free_shipping'=>'Free Shipping'] as $value=>$label)
                <option value="{{ $value }}" @selected(old('discount_type', $coupon->discount_type ?? 'percentage') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Discount Value *</label>
        <input type="number" name="discount_value" class="form-control"
               step="0.01" min="0"
               value="{{ old('discount_value', $coupon->discount_value ?? 0) }}">
        @error('discount_value') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Maximum Discount (₹)</label>
        <input type="number" name="maximum_discount" class="form-control"
               step="0.01" min="0"
               value="{{ old('maximum_discount', $coupon->maximum_discount ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Minimum Order Amount (₹)</label>
        <input type="number" name="minimum_order_amount" class="form-control"
               step="0.01" min="0"
               value="{{ old('minimum_order_amount', $coupon->minimum_order_amount ?? 0) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Total Usage Limit</label>
        <input type="number" name="usage_limit" class="form-control" min="1"
               value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Usage Limit Per Customer</label>
        <input type="number" name="usage_limit_per_user" class="form-control" min="1"
               value="{{ old('usage_limit_per_user', $coupon->usage_limit_per_user ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Start Date & Time</label>
        <input type="datetime-local" name="starts_at" class="form-control"
               value="{{ old('starts_at', isset($coupon?->starts_at) ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Expiry Date & Time</label>
        <input type="datetime-local" name="expires_at" class="form-control"
               value="{{ old('expires_at', isset($coupon?->expires_at) ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}">
    </div>

    <div class="col-12">
        <hr>
        <h5>Coupon Targeting</h5>
        <p class="text-muted small">Leave all three selections empty for a marketplace-wide coupon.</p>
    </div>

    <div class="col-md-4">
        <label class="form-label">Select Products</label>
        <select name="product_ids[]" class="form-select" multiple size="7">
            @foreach(\App\Models\Product::orderBy('name')->get(['id','name']) as $product)
                <option value="{{ $product->id }}"
                    @selected(in_array($product->id, $selectedProducts))>
                    {{ $product->name }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">Hold Ctrl to select multiple.</small>
        @error('product_ids.*') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Select Categories</label>
        <select name="category_ids[]" class="form-select" multiple size="7">
            @foreach(\App\Models\Category::orderBy('name')->get(['id','name']) as $category)
                <option value="{{ $category->id }}"
                    @selected(in_array($category->id, $selectedCategories))>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">Hold Ctrl to select multiple.</small>
        @error('category_ids.*') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Select Vendors</label>
        <select name="vendor_ids[]" class="form-select" multiple size="7">
            @foreach(\App\Models\Vendor::with('user')->orderBy('shop_name')->get() as $vendor)
                <option value="{{ $vendor->id }}"
                    @selected(in_array($vendor->id, $selectedVendors))>
                    {{ $vendor->shop_name }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">Hold Ctrl to select multiple.</small>
        @error('vendor_ids.*') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-4">
        <div class="form-check mt-3">
            <input type="hidden" name="first_order_only" value="0">
            <input type="checkbox" name="first_order_only" value="1"
                   class="form-check-input" id="firstOrder"
                   @checked(old('first_order_only', $coupon->first_order_only ?? false))>
            <label class="form-check-label" for="firstOrder">First Order Only</label>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-check mt-3">
            <input type="hidden" name="is_stackable" value="0">
            <input type="checkbox" name="is_stackable" value="1"
                   class="form-check-input" id="stackable"
                   @checked(old('is_stackable', $coupon->is_stackable ?? false))>
            <label class="form-check-label" for="stackable">Allow Stacking</label>
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label">Status *</label>
        <select name="status" class="form-select" required>
            <option value="active" @selected(old('status', $coupon->status ?? 'inactive') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $coupon->status ?? 'inactive') === 'inactive')>Inactive</option>
        </select>
    </div>

</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        {{ isset($coupon) ? 'Update Coupon' : 'Create Coupon' }}
    </button>
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-light">Cancel</a>
</div>
