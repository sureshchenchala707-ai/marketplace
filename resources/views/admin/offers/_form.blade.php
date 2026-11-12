
@csrf

@php
    $selectedProducts = old('product_ids', isset($offer) ? $offer->products->pluck('id')->toArray() : []);
    $selectedCategories = old('category_ids', isset($offer) ? $offer->categories->pluck('id')->toArray() : []);
    $selectedVendors = old('vendor_ids', isset($offer) ? $offer->vendors->pluck('id')->toArray() : []);
@endphp

<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label">Offer Name *</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $offer->name ?? '') }}" required>
        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Offer Type *</label>
        <select name="offer_type" id="offerType" class="form-select" required>
            @foreach([
                'percentage'=>'Percentage Discount',
                'fixed'=>'Fixed Discount',
                'free_shipping'=>'Free Shipping',
                'bogo'=>'Buy X Get Y (BOGO)'
            ] as $value=>$label)
                <option value="{{ $value }}" @selected(old('offer_type', $offer->offer_type ?? 'percentage') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="2">{{ old('description', $offer->description ?? '') }}</textarea>
    </div>

    <div class="col-md-6" id="discountValueField">
        <label class="form-label">Discount Value *</label>
        <input type="number" name="discount_value" class="form-control"
               step="0.01" min="0"
               value="{{ old('discount_value', $offer->discount_value ?? 0) }}">
        @error('discount_value') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Maximum Discount (₹)</label>
        <input type="number" name="maximum_discount" class="form-control"
               min="0" step="0.01"
               value="{{ old('maximum_discount', $offer->maximum_discount ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Minimum Order Amount (₹)</label>
        <input type="number" name="minimum_order_amount" class="form-control"
               min="0" step="0.01" required
               value="{{ old('minimum_order_amount', $offer->minimum_order_amount ?? 0) }}">
    </div>

    <!-- <div class="col-md-3" id="buyQuantityField">
        <label class="form-label">Buy Quantity *</label>
        <input type="number" name="buy_quantity" class="form-control"
               min="1" value="{{ old('buy_quantity', $offer->buy_quantity ?? 1) }}">
        @error('buy_quantity') <small class="text-danger">{{ $message }}</small> @enderror
    </div> -->
    <div class="col-md-3" id="buyQuantityField">
    <label class="form-label">Buy Quantity</label>
    <input type="number" name="buy_quantity" class="form-control"
           min="1" max="1"
           value="1" readonly>
    <small class="text-muted">Buy 1 item</small>
</div>

    <!-- <div class="col-md-3" id="getQuantityField">
        <label class="form-label">Get Quantity *</label>
        <input type="number" name="get_quantity" class="form-control"
               min="1" value="{{ old('get_quantity', $offer->get_quantity ?? 1) }}">
        @error('get_quantity') <small class="text-danger">{{ $message }}</small> @enderror
    </div> -->

    <div class="col-md-3" id="getQuantityField">
    <label class="form-label">Get Quantity</label>
    <input type="number" name="get_quantity" class="form-control"
           min="1" max="1"
           value="1" readonly>
    <small class="text-muted">Get 1 item free</small>
</div>

    <div class="col-md-6">
        <label class="form-label">Total Usage Limit</label>
        <input type="number" name="usage_limit" class="form-control"
               min="1" value="{{ old('usage_limit', $offer->usage_limit ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Priority</label>
        <input type="number" name="priority" class="form-control"
               min="0" max="10000" required
               value="{{ old('priority', $offer->priority ?? 0) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Start Date & Time</label>
        <input type="datetime-local" name="starts_at" class="form-control"
               value="{{ old('starts_at', isset($offer?->starts_at) ? $offer->starts_at->format('Y-m-d\TH:i') : '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Expiry Date & Time</label>
        <input type="datetime-local" name="expires_at" class="form-control"
               value="{{ old('expires_at', isset($offer?->expires_at) ? $offer->expires_at->format('Y-m-d\TH:i') : '') }}">
        @error('expires_at') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-12">
    <hr>
    <h5>Offer Targeting</h5>
    <div class="alert alert-info">
        <i class="fas fa-gift me-2"></i>
        <strong>Global BOGO:</strong>
        For Buy 1 Get 1 offers, leave Products, Categories
        and Vendors unselected. The offer applies to all
        eligible marketplace products. Only units of the
        same product qualify together.
    </div>
    <p class="text-muted small">
        For BOGO, all targeting selections must remain empty.
    </p>
</div>

    <div class="col-md-4">
        <label class="form-label">Products</label>
        <select name="product_ids[]" class="form-select" multiple size="7">
            @foreach(\App\Models\Product::orderBy('name')->get(['id','name']) as $product)
                <option value="{{ $product->id }}" @selected(in_array($product->id, $selectedProducts))>
                    {{ $product->name }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">Hold Ctrl to select multiple.</small>
    </div>

    <div class="col-md-4">
        <label class="form-label">Categories</label>
        <select name="category_ids[]" class="form-select" multiple size="7">
            @foreach(\App\Models\Category::orderBy('name')->get(['id','name']) as $category)
                <option value="{{ $category->id }}" @selected(in_array($category->id, $selectedCategories))>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Vendors</label>
        <select name="vendor_ids[]" class="form-select" multiple size="7">
            @foreach(\App\Models\Vendor::orderBy('shop_name')->get(['id','shop_name']) as $vendor)
                <option value="{{ $vendor->id }}" @selected(in_array($vendor->id, $selectedVendors))>
                    {{ $vendor->shop_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <div class="form-check mt-3">
            <input type="hidden" name="is_stackable" value="0">
            <input type="checkbox" name="is_stackable" value="1"
                   class="form-check-input" id="stackable"
                   @checked(old('is_stackable', $offer->is_stackable ?? false))>
            <label class="form-check-label" for="stackable">Allow Stacking</label>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Status *</label>
        <select name="status" class="form-select" required>
            <option value="active" @selected(old('status', $offer->status ?? 'inactive') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $offer->status ?? 'inactive') === 'inactive')>Inactive</option>
        </select>
    </div>

</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        {{ isset($offer) ? 'Update Offer' : 'Create Offer' }}
    </button>
    <a href="{{ route('admin.offers.index') }}" class="btn btn-light">Cancel</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const type = document.getElementById('offerType');
    const discount = document.getElementById('discountValueField');
    const buy = document.getElementById('buyQuantityField');
    const get = document.getElementById('getQuantityField');

    function updateOfferFields() {
        const isBogo = type.value === 'bogo';
        const isFreeShipping = type.value === 'free_shipping';

        discount.style.display = (isBogo || isFreeShipping) ? 'none' : '';
        buy.style.display = isBogo ? '' : 'none';
        get.style.display = isBogo ? '' : 'none';

        discount.querySelector('input').disabled = isBogo || isFreeShipping;
        buy.querySelector('input').disabled = !isBogo;
        get.querySelector('input').disabled = !isBogo;
    }

    type.addEventListener('change', updateOfferFields);
    updateOfferFields();
});
</script>
