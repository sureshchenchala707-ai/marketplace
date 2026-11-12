
@extends('layouts.app')

@section('title', $product->name . ' | ESS MARKETPLACE')

@section('content')

<style>
:root {
    --ess-blue: #0645e8;
    --ess-blue-dark: #0034bd;
    --ess-ice: #eaf2ff;
    --ess-ink: #101b35;
    --ess-muted: #71809b;
}

body {
    background: #f5f8ff;
}

.ess-breadcrumb {
    font-size: 12px;
    color: var(--ess-muted);
}

.ess-breadcrumb a {
    color: var(--ess-blue);
    text-decoration: none;
}

.ess-detail-card {
    background: white;
    border: 1px solid #e4ecfb;
    border-radius: 20px;
    padding: 25px;
}

.ess-gallery-main {
    background: linear-gradient(145deg,#e7f0ff,#f8fbff);
    border-radius: 17px;
    height: 440px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.ess-gallery-main img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 25px;
    transition: transform .4s ease;
}

.ess-gallery-main:hover img {
    transform: scale(1.05);
}

.ess-gallery-thumb {
    height: 78px;
    width: 78px;
    border: 2px solid #e4eaf5;
    border-radius: 12px;
    overflow: hidden;
    background: #f0f5ff;
    cursor: pointer;
    padding: 4px;
    transition: .25s;
}

.ess-gallery-thumb.active,
.ess-gallery-thumb:hover {
    border-color: var(--ess-blue);
    box-shadow: 0 0 0 3px rgba(6,69,232,.09);
}

.ess-gallery-thumb img {
    height: 100%;
    width: 100%;
    object-fit: contain;
}

.ess-detail-category {
    color: var(--ess-blue);
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 1px;
    font-weight: 800;
}

.ess-detail-title {
    font-size: clamp(25px,3vw,34px);
    font-weight: 900;
    color: var(--ess-ink);
    letter-spacing: -.8px;
    line-height: 1.25;
}

.ess-detail-price {
    font-size: 31px;
    font-weight: 900;
    color: var(--ess-blue);
}

.ess-detail-old-price {
    font-size: 16px;
    color: #9ba5b6;
    text-decoration: line-through;
}

.ess-discount {
    background: #e4f9ee;
    color: #13804b;
    border-radius: 30px;
    font-size: 11px;
    padding: 6px 11px;
    font-weight: 800;
}

.ess-stock {
    font-size: 12px;
    font-weight: 750;
}

.ess-variant {
    border: 1px solid #e2e9f5;
    border-radius: 11px;
    padding: 11px 14px;
    cursor: pointer;
    transition: .25s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin: 0 7px 8px 0;
    font-size: 12px;
    font-weight: 750;
}

.ess-variant:has(input:checked) {
    background: #edf3ff;
    border-color: var(--ess-blue);
    color: var(--ess-blue);
}

.ess-variant input {
    accent-color: var(--ess-blue);
}

.ess-primary-btn {
    background: var(--ess-blue);
    color: white;
    border: none;
    border-radius: 11px;
    padding: 14px 20px;
    font-weight: 800;
    transition: .25s;
}

.ess-primary-btn:hover {
    background: var(--ess-blue-dark);
    color: white;
    transform: translateY(-2px);
}

.ess-secondary-btn {
    border: 1px solid #cbd9f5;
    background: white;
    color: var(--ess-blue);
    border-radius: 11px;
    padding: 13px 17px;
    font-weight: 750;
}

.ess-info-line {
    display: flex;
    gap: 12px;
    align-items: center;
    padding: 13px 0;
    border-bottom: 1px solid #edf0f6;
    font-size: 12px;
}

.ess-info-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: var(--ess-ice);
    color: var(--ess-blue);
    display: flex;
    align-items: center;
    justify-content: center;
}

.ess-spec-table th {
    width: 35%;
    background: #f3f7ff;
    color: #263858;
    font-size: 12px;
}

.ess-spec-table td {
    font-size: 12px;
    color: #61708a;
}

.ess-review {
    border-bottom: 1px solid #e9edf5;
    padding: 19px 0;
}

.ess-stars {
    color: #f2ad00;
    letter-spacing: 2px;
}

.ess-related-card {
    background: white;
    border: 1px solid #e4ecfb;
    border-radius: 15px;
    overflow: hidden;
    height: 100%;
    transition: .3s;
}

.ess-related-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 14px 30px rgba(6,69,232,.1);
}

.ess-related-image {
    height: 175px;
    background: #edf3ff;
}

.ess-related-image img {
    height: 100%;
    width: 100%;
    object-fit: contain;
    padding: 13px;
}

.ess-related-info {
    padding: 13px;
}

.ess-related-info a {
    color: var(--ess-ink);
    text-decoration: none;
    font-size: 13px;
    font-weight: 750;
}

.ess-section-title {
    font-size: 21px;
    font-weight: 850;
    color: var(--ess-ink);
}

@media(max-width:767px) {
    .ess-gallery-main {
        height: 310px;
    }

    .ess-detail-card {
        padding: 17px;
    }

    .ess-gallery-thumb {
        height: 65px;
        width: 65px;
    }

    .ess-detail-price {
        font-size: 26px;
    }
}

@media(prefers-reduced-motion:reduce) {
    *,*::before,*::after {
        animation-duration:.01ms !important;
        transition-duration:.01ms !important;
    }
}
</style>

<div class="container-fluid px-lg-5 py-4">

    {{-- BREADCRUMB --}}
    <div class="ess-breadcrumb mb-4">
        <a href="{{ route('home') }}">Home</a>
        <i class="fas fa-chevron-right mx-2"></i>

        <a href="{{ route('home', ['category' => $product->category_id]) }}">
            {{ $product->category?->name }}
        </a>

        <i class="fas fa-chevron-right mx-2"></i>
        <span>{{ $product->name }}</span>
    </div>

    @php
        $primaryImage = $product->images->firstWhere('is_primary', true)
            ?? $product->images->first();

        $initialPrice = (float) $product->current_price;
        $discountPercent = $product->sale_price !== null
            && (float)$product->regular_price > 0
            ? round((1 - (float)$product->sale_price / (float)$product->regular_price) * 100)
            : 0;

        $totalStock = $product->variants->isNotEmpty()
            ? $product->variants->sum('stock_quantity')
            : $product->stock_quantity;
    @endphp

    <div class="row g-4">

        {{-- PRODUCT GALLERY --}}
        <div class="col-lg-6">

            <div class="ess-detail-card">

                <div class="ess-gallery-main mb-3" id="mainGallery">
                    @if($primaryImage)
                        <img id="mainProductImage"
                             src="{{ asset('storage/'.$primaryImage->image_path) }}"
                             alt="{{ $primaryImage->alt_text ?: $product->name }}">
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-image fa-4x mb-3"></i>
                            <p>No product image available.</p>
                        </div>
                    @endif
                </div>

                @if($product->images->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2">

                        @foreach($product->images as $index => $image)
                            <button type="button"
                                    class="ess-gallery-thumb {{ $image->is_primary ? 'active' : ($index === 0 ? 'active' : '') }}"
                                    data-image="{{ asset('storage/'.$image->image_path) }}"
                                    aria-label="View product image {{ $index + 1 }}">

                                <img src="{{ asset('storage/'.$image->image_path) }}"
                                     alt="{{ $image->alt_text ?: $product->name }}">
                            </button>
                        @endforeach

                    </div>
                @endif

            </div>

        </div>

        {{-- PRODUCT INFORMATION --}}
        <div class="col-lg-6">

            <div class="ess-detail-card h-100">

                <div class="ess-detail-category mb-2">
                    {{ $product->category?->name ?? 'Marketplace Product' }}
                </div>

                <h1 class="ess-detail-title mb-2">
                    {{ $product->name }}
                </h1>

                <div class="d-flex flex-wrap align-items-center gap-3 mb-3">

                    @if($reviewCount > 0)
                        <span class="ess-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= round($averageRating) ? '' : 'text-muted' }}"></i>
                            @endfor
                        </span>

                        <span class="small text-muted">
                            {{ number_format($averageRating, 1) }}
                            ({{ $reviewCount }} reviews)
                        </span>
                    @else
                        <span class="small text-muted">No ratings yet</span>
                    @endif

                    <span class="small text-muted">
                        SKU: {{ $product->sku }}
                    </span>

                </div>

                @if($product->brand)
                    <p class="small text-muted mb-3">
                        Brand: <strong class="text-dark">{{ $product->brand }}</strong>
                    </p>
                @endif

                <hr>

                {{-- PRICE --}}
                <div class="mb-4">

                    <div class="d-flex align-items-center flex-wrap gap-2">

                        <span class="ess-detail-price" id="displayPrice">
                            ₹{{ number_format($initialPrice, 2) }}
                        </span>

                        @if($product->sale_price !== null)
                            <span class="ess-detail-old-price"
                                  id="displayOldPrice">
                                ₹{{ number_format((float)$product->regular_price, 2) }}
                            </span>

                            @if($discountPercent > 0)
                                <span class="ess-discount" id="displayDiscount">
                                    {{ $discountPercent }}% OFF
                                </span>
                            @endif
                        @endif

                    </div>

                    <small class="text-muted">Inclusive of applicable taxes</small>

                </div>

                {{-- VARIANTS --}}
                @if($product->variants->isNotEmpty())

                    <div class="mb-4">

                        <h6 class="fw-bold mb-3">Choose Variant</h6>

                        <div id="variantOptions">

                            @foreach($product->variants as $index => $variant)

                                <label class="ess-variant">

                                    <input type="radio"
                                           name="selected_variant"
                                           value="{{ $variant->id }}"
                                           data-price="{{ $variant->sale_price ?? $variant->regular_price }}"
                                           data-regular="{{ $variant->regular_price }}"
                                           data-sale="{{ $variant->sale_price }}"
                                           data-stock="{{ $variant->stock_quantity }}"
                                           {{ $index === 0 ? 'checked' : '' }}>

                                    {{ $variant->variant_name }}

                                </label>

                            @endforeach

                        </div>

                        @foreach($product->variants as $variant)
                            <div class="small text-muted mb-1">
                                {{ $variant->variant_name }}:
                                {{ $variant->stock_quantity }} available
                            </div>
                        @endforeach

                    </div>

                @endif

                {{-- STOCK --}}
                <div class="mb-4">

                    @if($totalStock > 0)
                        <div class="ess-stock text-success" id="stockStatus">
                            <i class="fas fa-check-circle me-1"></i>
                            In Stock
                            <span class="text-muted fw-normal">
                                ({{ $totalStock }} available)
                            </span>
                        </div>
                    @else
                        <div class="ess-stock text-danger" id="stockStatus">
                            <i class="fas fa-times-circle me-1"></i>
                            Out of Stock
                        </div>
                    @endif

                </div>

                {{-- DESCRIPTION --}}
                @if($product->short_description)
                    <p class="text-secondary small mb-4">
                        {{ $product->short_description }}
                    </p>
                @endif

                {{-- PURCHASE ACTIONS --}}

{{-- PURCHASE ACTIONS --}}
<div class="mb-4">

    @auth

        @if(auth()->user()->hasRole('customer'))

            <form action="{{ route('account.cart.store') }}" method="POST">
                @csrf

                <input type="hidden" name="product_id" value="{{ $product->id }}">

                @if($product->variants->isNotEmpty())
                    <input type="hidden"
                           name="variant_id"
                           id="selectedVariantId"
                           value="{{ $product->variants->first()->id }}">
                @endif

                <div class="mb-3">
                    <label class="form-label fw-bold small">Quantity</label>
                    <input type="number"
                           name="quantity"
                           value="1"
                           min="1"
                           max="20"
                           class="form-control"
                           style="max-width:110px"
                           required>
                </div>

                <button type="submit"
                        class="ess-primary-btn w-100"
                        id="addToCartButton"
                        {{ $totalStock <= 0 ? 'disabled' : '' }}>
                    <i class="fas fa-shopping-cart me-2"></i>
                    Add to Cart
                </button>

            </form>

            <form action="{{ route('account.wishlist.store') }}"
                  method="POST"
                  class="mt-2">
                @csrf

                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <button type="submit" class="ess-secondary-btn w-100">
                    <i class="fas fa-heart me-2"></i>
                    Add to Wishlist
                </button>
            </form>

        @else

            <a href="{{ route('login') }}"
               class="ess-primary-btn d-block text-center text-decoration-none">
                Login as Customer to Shop
            </a>

        @endif

    @else

        <a href="{{ route('login') }}"
           class="ess-primary-btn d-block text-center text-decoration-none">
            Login to Add to Cart
        </a>

        <a href="{{ route('register') }}"
           class="ess-secondary-btn d-block text-center text-decoration-none mt-2">
            Create Customer Account
        </a>

    @endauth

</div>

                <div class="border rounded-3 p-3">

                    <div class="ess-info-line">
                        <div class="ess-info-icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Sold by</small>
                            <strong>{{ $product->vendor?->shop_name }}</strong>
                        </div>
                    </div>

                    <div class="ess-info-line">
                        <div class="ess-info-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <strong>Approved Marketplace Product</strong>
                            <small class="text-muted d-block">Seller-approved listing</small>
                        </div>
                    </div>

                    <div class="ess-info-line border-0">
                        <div class="ess-info-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div>
                            <strong>Product SKU</strong>
                            <small class="text-muted d-block">{{ $product->sku }}</small>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- PRODUCT DESCRIPTION AND SPECIFICATIONS --}}
    <div class="row mt-4 g-4">

        <div class="col-lg-7">

            <div class="ess-detail-card h-100">

                <h4 class="ess-section-title mb-3">
                    Product Description
                </h4>

                <div class="text-secondary" style="line-height:1.9">
                    {!! nl2br(e($product->description ?: 'No detailed description available.')) !!}
                </div>

            </div>

        </div>

        <div class="col-lg-5">

            <div class="ess-detail-card h-100">

                <h4 class="ess-section-title mb-3">
                    Product Specifications
                </h4>

                @if($product->specifications->isNotEmpty())

                    <div class="table-responsive">
                        <table class="table table-bordered ess-spec-table mb-0">
                            <tbody>

                                @foreach($product->specifications as $specification)
                                    <tr>
                                        <th>{{ $specification->attribute_name }}</th>
                                        <td>{{ $specification->attribute_value }}</td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>

                @else
                    <p class="text-muted small">No specifications available.</p>
                @endif

            </div>

        </div>

    </div>

    {{-- REVIEWS --}}
    <section class="ess-detail-card mt-4" id="reviews">

        <div class="d-flex justify-content-between flex-wrap align-items-center mb-3">

            <div>
                <h4 class="ess-section-title mb-1">Customer Reviews & Ratings</h4>
                <p class="text-muted small mb-0">
                    Feedback from customers about this product.
                </p>
            </div>

            <div class="text-end">

                @if($reviewCount > 0)
                    <div class="fs-3 fw-bold text-primary">
                        {{ number_format($averageRating, 1) }}/5
                    </div>
                    <small class="text-muted">{{ $reviewCount }} reviews</small>
                @else
                    <small class="text-muted">No ratings yet</small>
                @endif

            </div>

        </div>

        @forelse($product->reviews as $review)

            <div class="ess-review">

                <div class="d-flex justify-content-between flex-wrap gap-2">

                    <div>
                        <strong>{{ $review->user?->name ?? 'Customer' }}</strong>

                        <div class="ess-stars small mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-muted' }}"></i>
                            @endfor
                        </div>
                    </div>

                    <small class="text-muted">
                        {{ $review->created_at->format('d M Y') }}
                    </small>

                </div>

                @if($review->title)
                    <h6 class="fw-bold mt-2 mb-1">{{ $review->title }}</h6>
                @endif

                <p class="text-secondary small mb-1">
                    {{ $review->review }}
                </p>

                @if($review->is_verified_purchase)
                    <span class="text-success small">
                        <i class="fas fa-check-circle me-1"></i>Verified Purchase
                    </span>
                @endif

            </div>

        @empty

            <div class="text-center py-4">
                <i class="fas fa-star fa-2x text-warning mb-3"></i>
                <p class="text-muted mb-0">
                    No approved reviews yet. Be the first to share your experience!
                </p>
            </div>

        @endforelse

        <div class="mt-3">
            <a href="{{ route('login') }}"
               class="btn btn-outline-primary btn-sm">
                Login to review this product
            </a>
            <small class="text-muted ms-2">
                Review submission will be enabled with the customer order flow.
            </small>
        </div>

    </section>

    {{-- RELATED PRODUCTS --}}
    @if($relatedProducts->isNotEmpty())

        <section class="mt-5">

            <h4 class="ess-section-title mb-3">
                You May Also Like
            </h4>

            <div class="row g-3">

                @foreach($relatedProducts as $related)

                    @php
                        $relatedImage = $related->images->firstWhere('is_primary', true)
                            ?? $related->images->first();
                    @endphp

                    <div class="col-6 col-md-3">

                        <div class="ess-related-card">

                            <div class="ess-related-image">

                                @if($relatedImage)
                                    <img src="{{ asset('storage/'.$relatedImage->image_path) }}"
                                         alt="{{ $related->name }}"
                                         loading="lazy">
                                @else
                                    <div class="h-100 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image fa-2x text-muted"></i>
                                    </div>
                                @endif

                            </div>

                            <div class="ess-related-info">

                                <a href="{{ route('products.show', $related->slug) }}">
                                    {{ $related->name }}
                                </a>

                                <div class="fw-bold text-primary mt-2">
                                    ₹{{ number_format((float)$related->current_price, 2) }}
                                </div>

                                <small class="text-muted">
                                    {{ $related->vendor?->shop_name }}
                                </small>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </section>

    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Product image gallery
    const mainImage = document.getElementById('mainProductImage');

    document.querySelectorAll('.ess-gallery-thumb').forEach(function (thumb) {

        thumb.addEventListener('click', function () {

            if (!mainImage) return;

            mainImage.src = this.dataset.image;

            document.querySelectorAll('.ess-gallery-thumb')
                .forEach(item => item.classList.remove('active'));

            this.classList.add('active');
        });

    });

    // Dynamic variant price and stock display
    const variantInputs = document.querySelectorAll(
        'input[name="selected_variant"]'
    );

    const priceElement = document.getElementById('displayPrice');
    const oldPriceElement = document.getElementById('displayOldPrice');
    const discountElement = document.getElementById('displayDiscount');
    const stockElement = document.getElementById('stockStatus');

    function updateVariant() {

        const selected = document.querySelector(
            'input[name="selected_variant"]:checked'
        );

        if (!selected) return;

        const price = parseFloat(selected.dataset.price || 0);
        const regular = parseFloat(selected.dataset.regular || 0);
        const stock = parseInt(selected.dataset.stock || 0);

        const variantIdField = document.getElementById('selectedVariantId');

        if (variantIdField) {
            variantIdField.value = selected.value;
        }

        const addToCartButton = document.getElementById('addToCartButton');

        if (addToCartButton) {
            addToCartButton.disabled = stock <= 0;
        }


        priceElement.textContent = '₹' + price.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        if (oldPriceElement) {
            oldPriceElement.textContent = regular > price
                ? '₹' + regular.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })
                : '';

            oldPriceElement.style.display = regular > price ? 'inline' : 'none';
        }

        if (discountElement) {
            const discount = regular > price
                ? Math.round((1 - price / regular) * 100)
                : 0;

            discountElement.textContent = discount + '% OFF';
            discountElement.style.display = discount > 0 ? 'inline' : 'none';
        }

        if (stockElement) {
            stockElement.className = stock > 0
                ? 'ess-stock text-success'
                : 'ess-stock text-danger';

            stockElement.innerHTML = stock > 0
                ? '<i class="fas fa-check-circle me-1"></i> In Stock (' + stock + ' available)'
                : '<i class="fas fa-times-circle me-1"></i> Out of Stock';
        }
    }

    variantInputs.forEach(function (input) {
        input.addEventListener('change', updateVariant);
    });

    if (variantInputs.length > 0) {
        updateVariant();
    }

});
</script>

@endsection
