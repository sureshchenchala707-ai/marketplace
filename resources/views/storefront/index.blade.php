
@extends('layouts.app')

@section('title', 'ESS MARKETPLACE | Shop Smarter')

@section('content')

<style>
/* =========================================
   ESS MARKETPLACE - ELECTRIC BLUE STOREFRONT
========================================= */

.ess-home {
    --blue: #0645e8;
    --blue-dark: #0034bd;
    --blue-light: #dceaff;
    --text: #17213b;
    --muted: #7b8499;
    --surface: #f6f8fc;
    color: var(--text);
    background: var(--surface);
    padding-bottom: 1px;
}

.ess-home .ess-container {
    max-width: 1600px;
    margin: auto;
    padding: 30px clamp(14px, 3vw, 45px);
}

.ess-home a {
    text-decoration: none;
}

/* HERO */

.ess-home .hero {
    position: relative;
    overflow: hidden;
    min-height: 390px;
    display: flex;
    align-items: center;
    padding: 50px;
    border-radius: 25px;
    color: white;
    background: linear-gradient(115deg, #002b9c, #0645e8 58%, #3885ff);
    isolation: isolate;
}

.ess-home .hero::before,
.ess-home .hero::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,.09);
    z-index: -1;
    pointer-events: none;
}

.ess-home .hero::before {
    width: 390px;
    height: 390px;
    right: 5%;
    top: -170px;
}

.ess-home .hero::after {
    width: 270px;
    height: 270px;
    right: 28%;
    bottom: -175px;
}

.ess-home .hero-content {
    max-width: 570px;
    position: relative;
    z-index: 2;
    animation: essRise .7s ease both;
}

.ess-home .hero-label {
    display: inline-block;
    padding: 8px 15px;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 30px;
    background: rgba(255,255,255,.13);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.2px;
}

.ess-home .hero h1 {
    margin: 22px 0 15px;
    font-size: clamp(36px, 5vw, 60px);
    line-height: 1.08;
    letter-spacing: -2px;
    font-weight: 900;
}

.ess-home .hero h1 span {
    color: #b9d4ff;
}

.ess-home .hero p {
    max-width: 440px;
    color: #e0ebff;
    font-size: 15px;
    line-height: 1.8;
}

.ess-home .hero-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    margin-top: 12px;
    padding: 14px 24px;
    border-radius: 50px;
    background: white;
    color: var(--blue-dark);
    font-size: 13px;
    font-weight: 800;
    transition: .25s;
}

.ess-home .hero-btn:hover {
    color: var(--blue-dark);
    transform: translateY(-4px);
    box-shadow: 0 12px 25px rgba(0,0,0,.2);
}

.ess-home .hero-art {
    position: absolute;
    right: 5%;
    top: 50%;
    transform: translateY(-50%);
    width: 37%;
    max-width: 390px;
    animation: essFloat 5s ease-in-out infinite;
}

.ess-home .hero-art img {
    width: 100%;
    height: 290px;
    object-fit: contain;
    filter: drop-shadow(0 25px 30px rgba(0,0,0,.25));
}

/* BENEFITS */

.ess-home .benefit {
    height: 100%;
    padding: 20px 12px;
    text-align: center;
    border: 1px solid #e8edfa;
    border-radius: 16px;
    background: white;
    transition: .25s;
}

.ess-home .benefit:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(6,69,232,.08);
}

.ess-home .benefit-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 46px;
    height: 46px;
    margin: 0 auto 12px;
    border-radius: 14px;
    background: #eaf2ff;
    color: var(--blue);
    font-size: 18px;
}

/* SECTION HEADINGS */

.ess-home .section-heading h2 {
    margin-bottom: 5px;
    color: var(--text);
    font-size: 25px;
    font-weight: 850;
    letter-spacing: -.7px;
}

.ess-home .section-heading p {
    margin: 0;
    color: var(--muted);
    font-size: 13px;
}

.ess-home .section-link {
    color: var(--blue);
    font-size: 13px;
    font-weight: 800;
}

/* CATEGORIES */

.ess-home .category-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 11px;
    height: 100%;
    padding: 20px 10px;
    border: 1px solid #e7edfa;
    border-radius: 17px;
    background: white;
    color: var(--text);
    text-align: center;
    transition: .25s;
}

.ess-home .category-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 55px;
    height: 55px;
    border-radius: 17px;
    background: #eaf2ff;
    color: var(--blue);
    font-size: 21px;
    transition: .25s;
}

.ess-home .category-card:hover {
    color: var(--blue);
    border-color: #9bbcff;
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(6,69,232,.09);
}

.ess-home .category-card:hover .category-icon {
    color: white;
    background: var(--blue);
}

/* PRODUCT CARDS */

.ess-home .product-card {
    position: relative;
    height: 100%;
    overflow: hidden;
    border: 1px solid #e1eaff;
    border-radius: 19px;
    background: white;
    box-shadow: 0 5px 18px rgba(20,65,180,.06);
    transition: .3s;
}

.ess-home .product-card:hover {
    transform: translateY(-7px);
    border-color: #9bbcff;
    box-shadow: 0 15px 32px rgba(6,69,232,.13);
}

.ess-home .product-image {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 235px;
    overflow: hidden;
    background: linear-gradient(145deg, #e8f1ff, #c5dcff);
}

.ess-home .product-image img {
    width: 100%;
    height: 100%;
    padding: 16px;
    object-fit: contain;
    transition: transform .45s;
}

.ess-home .product-card:hover .product-image img {
    transform: scale(1.08);
}

.ess-home .product-badge {
    position: absolute;
    top: 13px;
    left: 12px;
    z-index: 2;
    padding: 6px 10px;
    border-radius: 30px;
    color: white;
    background: var(--blue);
    font-size: 9px;
    font-weight: 800;
    letter-spacing: .4px;
}

.ess-home .wishlist-decoration {
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    color: #24324e;
    background: rgba(255,255,255,.95);
    box-shadow: 0 3px 10px rgba(0,0,0,.09);
}

.ess-home .product-info {
    padding: 15px;
}

.ess-home .product-category {
    margin-bottom: 6px;
    color: var(--muted);
    font-size: 11px;
    font-weight: 600;
}

.ess-home .product-name {
    min-height: 40px;
    margin: 0 0 9px;
    color: var(--text);
    font-size: 14px;
    font-weight: 800;
    line-height: 1.45;
}

.ess-home .product-name a {
    color: inherit;
}

.ess-home .product-name a:hover {
    color: var(--blue);
}

.ess-home .product-price {
    color: var(--blue-dark);
    font-size: 17px;
    font-weight: 900;
    white-space: nowrap;
}

.ess-home .old-price {
    margin-left: 5px;
    color: #9298a5;
    font-size: 11px;
    font-weight: 500;
    text-decoration: line-through;
}

.ess-home .vendor-name {
    margin-top: 9px;
    color: var(--muted);
    font-size: 10px;
}

.ess-home .view-product {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    margin-top: 14px;
    padding: 11px;
    border-radius: 30px;
    color: white;
    background: linear-gradient(100deg, #0645e8, #3885ff);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .4px;
    transition: .25s;
}

.ess-home .view-product:hover {
    color: white;
    background: var(--blue-dark);
}

/* PROMO */

.ess-home .promo {
    position: relative;
    overflow: hidden;
    padding: 32px;
    border-radius: 20px;
    background: linear-gradient(110deg, #dceaff, #f1f6ff);
}

.ess-home .promo h3 {
    color: var(--blue-dark);
    font-weight: 850;
}

.ess-home .promo p {
    color: #65738d;
    font-size: 13px;
}

.ess-home .promo-btn {
    display: inline-block;
    padding: 11px 18px;
    border-radius: 10px;
    color: white;
    background: var(--blue);
    font-size: 12px;
    font-weight: 800;
}

.ess-home .promo-btn:hover {
    color: white;
    background: var(--blue-dark);
}

/* FILTERS */

.ess-home .filter-panel {
    padding: 18px;
    border: 1px solid #e5ebf7;
    border-radius: 16px;
    background: white;
}

.ess-home .form-control,
.ess-home .form-select {
    min-height: 43px;
    border: 1px solid #e1e8f5;
    border-radius: 9px;
    font-size: 12px;
}

.ess-home .form-control:focus,
.ess-home .form-select:focus {
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(6,69,232,.09);
}

.ess-home .filter-btn {
    min-height: 43px;
    width: 100%;
    border: 0;
    border-radius: 9px;
    color: white;
    background: var(--blue);
    font-size: 12px;
    font-weight: 800;
}

.ess-home .filter-btn:hover {
    background: var(--blue-dark);
}

/* FOOTER */

.ess-home-footer {
    margin-top: 55px;
    padding: 45px 0 22px;
    color: white;
    background: #101d3d;
}

.ess-home-footer h6 {
    font-weight: 800;
}

.ess-home-footer p,
.ess-home-footer a {
    color: #bdc9df;
    font-size: 12px;
    text-decoration: none;
}

.ess-home-footer a:hover {
    color: white;
}

/* PAGINATION */

.ess-home .pagination {
    gap: 5px;
}

.ess-home .page-link {
    border: 0;
    border-radius: 8px !important;
    color: var(--blue);
    font-size: 12px;
}

.ess-home .page-item.active .page-link {
    color: white;
    background: var(--blue);
}

/* ANIMATIONS */

@keyframes essRise {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes essFloat {
    0%, 100% {
        transform: translateY(-50%);
    }
    50% {
        transform: translateY(calc(-50% - 12px));
    }
}

/* RESPONSIVE */

@media(max-width: 991px) {
    .ess-home .hero {
        padding: 38px 30px;
    }

    .ess-home .hero-art {
        right: -5%;
        width: 48%;
        opacity: .35;
    }
}

@media(max-width: 575px) {
    .ess-home .ess-container {
        padding: 20px 13px;
    }

    .ess-home .hero {
        min-height: 350px;
        padding: 28px 22px;
        border-radius: 18px;
    }

    .ess-home .hero h1 {
        font-size: 38px;
    }

    .ess-home .hero-art {
        right: -15%;
        width: 70%;
        opacity: .15;
    }

    .ess-home .product-image {
        height: 165px;
    }

    .ess-home .product-info {
        padding: 11px;
    }

    .ess-home .product-name {
        font-size: 12px;
    }

    .ess-home .product-price {
        font-size: 15px;
    }

    .ess-home .section-heading h2 {
        font-size: 21px;
    }

    .ess-home .promo {
        padding: 23px;
    }
}

@media(prefers-reduced-motion: reduce) {
    .ess-home *,
    .ess-home *::before,
    .ess-home *::after {
        animation-duration: .01ms !important;
        transition-duration: .01ms !important;
    }
}
</style>

<div class="ess-home">
    <div class="ess-container">

        {{-- HERO --}}
        <section class="hero mb-4">
            <div class="hero-content">

                <span class="hero-label">
                    <i class="fas fa-star me-2"></i>
                    YOUR ONE-STOP SHOPPING DESTINATION
                </span>

                <h1>
                    Discover More.<br>
                    Shop <span>Smarter.</span>
                </h1>

                <p>
                    Explore amazing products from trusted marketplace sellers.
                    Find what you love, compare your choices and shop with confidence.
                </p>

                <a href="#products" class="hero-btn">
                    Explore Products
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="hero-art d-none d-md-block">
                @if($featuredProducts->isNotEmpty())
                    @php
                        $heroImage = $featuredProducts->first()->images->firstWhere('is_primary', true)
                            ?? $featuredProducts->first()->images->first();
                    @endphp

                    @if($heroImage)
                        <img
                            src="{{ asset('storage/' . $heroImage->image_path) }}"
                            alt="Featured product">
                    @else
                        <div class="text-center">
                            <i class="fas fa-shopping-bag"
                               style="font-size:170px;color:rgba(255,255,255,.5)"></i>
                        </div>
                    @endif
                @else
                    <div class="text-center">
                        <i class="fas fa-shopping-bag"
                           style="font-size:170px;color:rgba(255,255,255,.5)"></i>
                    </div>
                @endif
            </div>
        </section>

        {{-- BENEFITS --}}
        <section class="row g-3 mb-5">

            <div class="col-6 col-lg-3">
                <div class="benefit">
                    <div class="benefit-icon"><i class="fas fa-store"></i></div>
                    <div class="fw-bold small">Multiple Sellers</div>
                    <small class="text-muted">More choices</small>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="benefit">
                    <div class="benefit-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="fw-bold small">Trusted Products</div>
                    <small class="text-muted">Approved listings</small>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="benefit">
                    <div class="benefit-icon"><i class="fas fa-tags"></i></div>
                    <div class="fw-bold small">Great Choices</div>
                    <small class="text-muted">Explore collections</small>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="benefit">
                    <div class="benefit-icon"><i class="fas fa-headset"></i></div>
                    <div class="fw-bold small">Customer Support</div>
                    <small class="text-muted">Here to help</small>
                </div>
            </div>

        </section>

        {{-- CATEGORIES --}}
        <section class="mb-5">

            <div class="section-heading mb-3">
                <h2>Explore Categories</h2>
                <p>Find products from your favourite categories.</p>
            </div>

            @php
                $categoryIcons = [
                    'electronics' => 'fa-mobile-alt',
                    'fashion' => 'fa-shirt',
                    'clothing' => 'fa-tshirt',
                    'home' => 'fa-couch',
                    'beauty' => 'fa-spa',
                    'sports' => 'fa-futbol',
                    'books' => 'fa-book',
                    'toys' => 'fa-gamepad',
                    'grocery' => 'fa-shopping-basket',
                    'automotive' => 'fa-car',
                ];
            @endphp

            <div class="row g-3">
                @forelse($categories as $category)

                    @php
                        $icon = 'fa-th-large';
                        $categoryKey = strtolower($category->name);

                        foreach($categoryIcons as $key => $value) {
                            if(str_contains($categoryKey, $key)) {
                                $icon = $value;
                                break;
                            }
                        }
                    @endphp

                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('home', ['category' => $category->id]) }}"
                           class="category-card">

                            <div class="category-icon">
                                <i class="fas {{ $icon }}"></i>
                            </div>

                            <span class="fw-bold small">
                                {{ $category->name }}
                            </span>

                        </a>
                    </div>

                @empty

                    <div class="col-12">
                        <div class="alert alert-light border">
                            Categories will appear here soon.
                        </div>
                    </div>

                @endforelse
            </div>
        </section>

        {{-- FEATURED PRODUCTS --}}
        @if($featuredProducts->isNotEmpty())

            <section class="mb-5">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="section-heading">
                        <h2>Featured Collection</h2>
                        <p>Handpicked products from our marketplace.</p>
                    </div>

                    <a href="{{ route('home', ['featured' => 1]) }}"
                       class="section-link">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="row g-3">
                    @foreach($featuredProducts as $product)

                        @php
                            $image = $product->images->firstWhere('is_primary', true)
                                ?? $product->images->first();
                        @endphp

                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="product-card">

                                <div class="product-image">

                                    <span class="product-badge">
                                        <i class="fas fa-star me-1"></i>
                                        FEATURED
                                    </span>

                                    <div class="wishlist-decoration">
                                        <i class="far fa-heart"></i>
                                    </div>

                                    @if($image)
                                        <img
                                            src="{{ asset('storage/' . $image->image_path) }}"
                                            alt="{{ $product->name }}"
                                            loading="lazy">
                                    @else
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    @endif

                                </div>

                                <div class="product-info">

                                    <div class="product-category">
                                        {{ $product->category?->name ?? 'Product' }}
                                    </div>

                                    <h3 class="product-name">
                                        <a href="{{ route('products.show', $product->slug) }}">
                                            {{ $product->name }}
                                        </a>
                                    </h3>

                                    <div class="product-price">
                                        ₹{{ number_format((float)$product->current_price, 2) }}

                                        @if($product->sale_price !== null)
                                            <span class="old-price">
                                                ₹{{ number_format((float)$product->regular_price, 2) }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="vendor-name">
                                        <i class="fas fa-store me-1"></i>
                                        {{ $product->vendor?->shop_name ?? 'ESS Seller' }}
                                    </div>

                                    <a href="{{ route('products.show', $product->slug) }}"
                                       class="view-product">
                                        View Product
                                        <i class="fas fa-arrow-right"></i>
                                    </a>

                                </div>
                            </div>
                        </div>

                    @endforeach
                </div>
            </section>

        @endif

        {{-- PROMOTIONAL BANNER --}}
        <section class="promo mb-5">

            <div class="row align-items-center g-3">

                <div class="col-md-8">
                    <span class="text-uppercase fw-bold small"
                          style="color:#0645e8;letter-spacing:1px">
                        ESS MARKETPLACE
                    </span>

                    <h3 class="mt-2">
                        Your next favourite find is waiting.
                    </h3>

                    <p class="mb-0">
                        Browse our marketplace and discover products across categories.
                    </p>
                </div>

                <div class="col-md-4 text-md-end">
                    <a href="#products" class="promo-btn">
                        Explore Marketplace
                        <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

            </div>
        </section>

        {{-- ALL PRODUCTS --}}
        <section id="products">

            <div class="section-heading mb-3">
                <h2>Explore Our Products</h2>
                <p>Discover the latest products available in our marketplace.</p>
            </div>

            {{-- FILTERS --}}
            <form action="{{ route('home') }}"
                  method="GET"
                  class="filter-panel mb-4">

                <div class="row g-2 align-items-end">

                    <div class="col-6 col-lg-3">
                        <label class="form-label small fw-bold">Category</label>

                        <select name="category" class="form-select">
                            <option value="">All Categories</option>

                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>

                                @foreach($category->children as $child)
                                    <option value="{{ $child->id }}"
                                        {{ request('category') == $child->id ? 'selected' : '' }}>
                                        — {{ $child->name }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-lg-2">
                        <label class="form-label small fw-bold">Min Price</label>
                        <input type="number"
                               name="min_price"
                               class="form-control"
                               min="0"
                               value="{{ request('min_price') }}"
                               placeholder="₹ Min">
                    </div>

                    <div class="col-6 col-lg-2">
                        <label class="form-label small fw-bold">Max Price</label>
                        <input type="number"
                               name="max_price"
                               class="form-control"
                               min="0"
                               value="{{ request('max_price') }}"
                               placeholder="₹ Max">
                    </div>

                    <div class="col-6 col-lg-3">
                        <label class="form-label small fw-bold">Sort Products</label>

                        <select name="sort" class="form-select">
                            <option value="latest"
                                {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>
                                Latest Products
                            </option>

                            <option value="price_low"
                                {{ request('sort') == 'price_low' ? 'selected' : '' }}>
                                Price: Low to High
                            </option>

                            <option value="price_high"
                                {{ request('sort') == 'price_high' ? 'selected' : '' }}>
                                Price: High to Low
                            </option>

                            <option value="name"
                                {{ request('sort') == 'name' ? 'selected' : '' }}>
                                Product Name
                            </option>
                        </select>
                    </div>

                    <div class="col-6 col-lg-2">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter me-1"></i>
                            Apply Filters
                        </button>
                    </div>

                    @if(request()->hasAny([
                        'search',
                        'category',
                        'min_price',
                        'max_price',
                        'sort',
                        'featured'
                    ]))
                        <div class="col-12">
                            <a href="{{ route('home') }}"
                               class="small text-danger">
                                <i class="fas fa-times me-1"></i>
                                Clear all filters
                            </a>
                        </div>
                    @endif

                </div>
            </form>

            {{-- RESULTS COUNT --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">

                <span class="text-muted small">
                    Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}
                    of {{ $products->total() }} products
                </span>

                @if(request('search'))
                    <span class="badge rounded-pill"
                          style="background:#dceaff;color:#0034bd">
                        Search: {{ request('search') }}
                    </span>
                @endif

            </div>

            {{-- PRODUCT GRID --}}
            <div class="row g-3">

                @forelse($products as $product)

                    @php
                        $image = $product->images->firstWhere('is_primary', true)
                            ?? $product->images->first();
                    @endphp

                    <div class="col-6 col-md-4 col-lg-3">

                        <div class="product-card">

                            <div class="product-image">

                                @if($product->is_featured)
                                    <span class="product-badge">FEATURED</span>
                                @endif

                                <div class="wishlist-decoration">
                                    <i class="far fa-heart"></i>
                                </div>

                                @if($image)
                                    <img
                                        src="{{ asset('storage/' . $image->image_path) }}"
                                        alt="{{ $product->name }}"
                                        loading="lazy">
                                @else
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                @endif

                            </div>

                            <div class="product-info">

                                <div class="product-category">
                                    {{ $product->category?->name ?? 'Product' }}
                                </div>

                                <h3 class="product-name">
                                    <a href="{{ route('products.show', $product->slug) }}">
                                        {{ $product->name }}
                                    </a>
                                </h3>

                                <div class="product-price">
                                    ₹{{ number_format((float)$product->current_price, 2) }}

                                    @if($product->sale_price !== null)
                                        <span class="old-price">
                                            ₹{{ number_format((float)$product->regular_price, 2) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="vendor-name">
                                    <i class="fas fa-store me-1"></i>
                                    {{ $product->vendor?->shop_name ?? 'ESS Seller' }}
                                </div>

                                <a href="{{ route('products.show', $product->slug) }}"
                                   class="view-product">
                                    View Product
                                    <i class="fas fa-arrow-right"></i>
                                </a>

                            </div>
                        </div>

                    </div>

                @empty

                    <div class="col-12">
                        <div class="bg-white border rounded-4 text-center p-5">

                            <i class="fas fa-search fa-3x text-muted mb-3"></i>

                            <h5 class="fw-bold">No Products Found</h5>

                            <p class="text-muted small">
                                Try another search term or clear your filters.
                            </p>

                            <a href="{{ route('home') }}"
                               class="btn btn-primary rounded-pill px-4">
                                Explore All Products
                            </a>

                        </div>
                    </div>

                @endforelse

            </div>

            {{-- PAGINATION --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>

        </section>

    </div>
</div>

{{-- FOOTER --}}
<footer class="ess-home-footer">

    <div class="container-fluid px-lg-5">

        <div class="row g-4">

            <div class="col-lg-4">
                <a href="{{ route('home') }}"
                   class="text-white fw-bold fs-4">
                    ESS<span style="color:#70a5ff">.</span>MARKETPLACE
                </a>

                <p class="mt-3">
                    Discover more, shop smarter.
                    Your destination for products from multiple marketplace sellers.
                </p>
            </div>

            <div class="col-6 col-lg-2">
                <h6>Marketplace</h6>
                <p><a href="{{ route('home') }}">Home</a></p>
                <p><a href="#products">Products</a></p>
                <p><a href="#products">Categories</a></p>
            </div>

            <div class="col-6 col-lg-2">
                <h6>My Account</h6>
                <p><a href="{{ route('login') }}">Customer Login</a></p>
                <p><a href="{{ route('register') }}">Register</a></p>
                <p><a href="{{ route('account.dashboard') }}">My Account</a></p>
            </div>

            <div class="col-6 col-lg-2">
                <h6>For Sellers</h6>
                <p><a href="{{ route('vendor.login') }}">Vendor Login</a></p>
                <p><a href="{{ route('vendor.register') }}">Become a Seller</a></p>
            </div>

        </div>

        <hr class="border-secondary my-4">

        <div class="d-flex flex-wrap justify-content-between gap-2">
            <small class="text-white-50">
                © {{ date('Y') }} ESS MARKETPLACE. All rights reserved.
            </small>

            <small class="text-white-50">
                Built for smarter shopping.
            </small>
        </div>

    </div>

</footer>

@endsection
