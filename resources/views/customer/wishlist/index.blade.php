
@extends('layouts.app')

@section('title', 'My Wishlist | ESS MARKETPLACE')

@section('content')

<style>
     .btn-primary {
    padding: 15px 20px !important;
    min-height: 40px !important;
}
.ess-wishlist-card {
    background: white;
    border: 1px solid #e2eaff;
    border-radius: 16px;
    overflow: hidden;
    height: 100%;
    transition: .3s;
}
.ess-wishlist-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 28px rgba(6,69,232,.1);
}
.ess-wishlist-image {
    height: 205px;
    background: #edf4ff;
}
.ess-wishlist-image img {
    height: 100%;
    width: 100%;
    object-fit: contain;
    padding: 15px;
}
.ess-wishlist-info {
    padding: 15px;
}
.ess-wishlist-info a {
    text-decoration: none;
    color: #101b35;
    font-weight: 750;
}
.ess-wishlist-btn {
    background: #0645e8;
    color: white;
    border: 0;
    border-radius: 9px;
    padding: 9px 12px;
    font-size: 12px;
    font-weight: 750;
}
</style>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold">My Wishlist <i class="fas fa-heart text-danger"></i></h3>
            <p class="text-muted small mb-0">
                Your favourite products in one place.
            </p>
        </div>

        <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm">
            Continue Shopping
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($wishlistItems->isEmpty())

        <div class="bg-white border rounded-4 text-center p-5">
            <i class="far fa-heart fa-3x text-danger mb-3"></i>
            <h5 class="fw-bold">Your wishlist is empty</h5>
            <p class="text-muted small">
                Save your favourite products here.
            </p>
            <a href="{{ route('home') }}"
               class="btn btn-primary rounded-pill px-4">
                Explore Products
            </a>
        </div>

    @else

        <div class="row g-3">

            @foreach($wishlistItems as $item)

                @php
                    $product = $item->product;
                    $image = $product->images->firstWhere('is_primary', true)
                        ?? $product->images->first();
                @endphp

                <div class="col-6 col-md-4 col-lg-3">

                    <div class="ess-wishlist-card">

                        <div class="ess-wishlist-image">

                            @if($image)
                                <img src="{{ asset('storage/'.$image->image_path) }}"
                                     alt="{{ $product->name }}">
                            @else
                                <div class="h-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                </div>
                            @endif

                        </div>

                        <div class="ess-wishlist-info">

                            <a href="{{ route('products.show', $product->slug) }}">
                                {{ $product->name }}
                            </a>

                            <div class="text-primary fw-bold mt-2">
                                ₹{{ number_format((float)$product->current_price, 2) }}
                            </div>

                            <small class="text-muted d-block mb-3">
                                {{ $product->vendor?->shop_name }}
                            </small>

                            <div class="d-flex gap-2">

                                <a href="{{ route('products.show', $product->slug) }}"
                                   class="btn btn-outline-primary btn-sm flex-grow-1">
                                    View
                                </a>

                                <form action="{{ route('account.wishlist.destroy', $item) }}"
                                      method="POST"
                                      onsubmit="return confirm('Remove this product from wishlist?')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

        <div class="mt-4">
            {{ $wishlistItems->links() }}
        </div>

    @endif

</div>

@endsection
