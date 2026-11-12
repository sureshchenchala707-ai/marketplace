
@extends('layouts.app')

@section('title', 'My Account | ESS MARKETPLACE')

@section('content')

<div class="ess-customer tw-mx-auto tw-w-full tw-max-w-7xl tw-px-4 tw-py-6 sm:tw-px-6 lg:tw-px-8">

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="tw-mb-6 tw-flex tw-items-center tw-gap-3 tw-rounded-xl tw-border tw-border-green-200 tw-bg-green-50 tw-p-4 tw-text-sm tw-font-medium tw-text-green-800">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- WELCOME BANNER --}}
    <div class="tw-relative tw-mb-8 tw-overflow-hidden tw-rounded-3xl tw-bg-gradient-to-r tw-from-ess-dark tw-to-ess-accent tw-p-7 tw-text-white tw-shadow-elevated sm:tw-p-10">
        <div class="tw-pointer-events-none tw-absolute -tw-right-10 -tw-top-16 tw-h-64 tw-w-64 tw-rounded-full tw-bg-white/10"></div>
        <div class="tw-pointer-events-none tw-absolute tw-bottom-[-90px] tw-right-40 tw-h-56 tw-w-56 tw-rounded-full tw-bg-white/10"></div>

        <div class="tw-relative tw-z-10">
            <div class="tw-mb-3 tw-inline-flex tw-items-center tw-gap-2 tw-rounded-full tw-border tw-border-white/20 tw-bg-white/10 tw-px-3 tw-py-1.5 tw-text-xs tw-font-semibold">
                <i class="fas fa-sparkles"></i>
                CUSTOMER DASHBOARD
            </div>

            <h2 class="tw-mb-3 tw-text-2xl tw-font-extrabold sm:tw-text-3xl lg:tw-text-4xl">
                Welcome, {{ auth()->user()->name }}!
            </h2>

            <p class="tw-mb-0 tw-max-w-xl tw-text-sm tw-leading-6 tw-text-white/80 sm:tw-text-base">
                Manage your shopping, wishlist and account from one place.
            </p>
        </div>
    </div>

    {{-- SECTION HEADING --}}
    <div class="tw-mb-5 tw-flex tw-flex-wrap tw-items-end tw-justify-between tw-gap-3">
        <div>
            <p class="tw-mb-1 tw-text-xs tw-font-bold tw-uppercase tw-tracking-widest tw-text-ess-primary">
                YOUR ACCOUNT
            </p>
            <h3 class="tw-mb-0 tw-text-xl tw-font-extrabold tw-text-ess-text sm:tw-text-2xl">
                My Shopping Dashboard
            </h3>
        </div>

        <a href="{{ route('home') }}"
           class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-xl tw-border tw-border-blue-100 tw-bg-white tw-px-4 tw-py-2.5 tw-text-sm tw-font-bold tw-text-ess-primary tw-no-underline tw-transition hover:tw-bg-blue-50">
            <i class="fas fa-store"></i>
            Continue Shopping
        </a>
    </div>

    {{-- DASHBOARD CARDS --}}
    <div class="tw-grid tw-grid-cols-1 tw-gap-5 sm:tw-grid-cols-2 xl:tw-grid-cols-3">

        {{-- CART --}}
        <div class="tw-group tw-rounded-2xl tw-border tw-border-blue-100 tw-bg-white tw-p-6 tw-shadow-card tw-transition-all tw-duration-300 hover:-tw-translate-y-1 hover:tw-shadow-elevated">
            <div class="tw-mb-5 tw-flex tw-h-14 tw-w-14 tw-items-center tw-justify-center tw-rounded-2xl tw-bg-blue-50 tw-text-xl tw-text-ess-primary">
                <i class="fas fa-shopping-cart"></i>
            </div>

            <h5 class="tw-mb-2 tw-text-lg tw-font-extrabold tw-text-ess-text">My Cart</h5>

            <p class="tw-mb-5 tw-text-sm tw-leading-6 tw-text-ess-muted">
                {{ auth()->user()->cartItems()->count() }}
                cart items saved.
            </p>

            <a href="{{ route('account.cart.index') }}"
               class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-xl tw-bg-ess-primary tw-px-4 tw-py-2.5 tw-text-sm tw-font-bold tw-text-white tw-no-underline tw-transition hover:tw-bg-ess-dark">
                View Cart <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        {{-- WISHLIST --}}
        <div class="tw-group tw-rounded-2xl tw-border tw-border-blue-100 tw-bg-white tw-p-6 tw-shadow-card tw-transition-all tw-duration-300 hover:-tw-translate-y-1 hover:tw-shadow-elevated">
            <div class="tw-mb-5 tw-flex tw-h-14 tw-w-14 tw-items-center tw-justify-center tw-rounded-2xl tw-bg-pink-50 tw-text-xl tw-text-pink-600">
                <i class="fas fa-heart"></i>
            </div>

            <h5 class="tw-mb-2 tw-text-lg tw-font-extrabold tw-text-ess-text">My Wishlist</h5>

            <p class="tw-mb-5 tw-text-sm tw-leading-6 tw-text-ess-muted">
                {{ auth()->user()->wishlistItems()->count() }}
                favourite products saved.
            </p>

            <a href="{{ route('account.wishlist.index') }}"
               class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-xl tw-bg-ess-primary tw-px-4 tw-py-2.5 tw-text-sm tw-font-bold tw-text-white tw-no-underline tw-transition hover:tw-bg-ess-dark">
                View Wishlist <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        {{-- ORDERS --}}
        <div class="tw-group tw-rounded-2xl tw-border tw-border-blue-100 tw-bg-white tw-p-6 tw-shadow-card tw-transition-all tw-duration-300 hover:-tw-translate-y-1 hover:tw-shadow-elevated">
            <div class="tw-mb-5 tw-flex tw-h-14 tw-w-14 tw-items-center tw-justify-center tw-rounded-2xl tw-bg-violet-50 tw-text-xl tw-text-violet-600">
                <i class="fas fa-box"></i>
            </div>

            <h5 class="tw-mb-2 tw-text-lg tw-font-extrabold tw-text-ess-text">My Orders</h5>

            <p class="tw-mb-5 tw-text-sm tw-leading-6 tw-text-ess-muted">
                View your order history and track your purchases.
            </p>

            <a href="{{ route('account.orders.index') }}"
               class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-xl tw-bg-ess-primary tw-px-4 tw-py-2.5 tw-text-sm tw-font-bold tw-text-white tw-no-underline tw-transition hover:tw-bg-ess-dark">
                View My Orders <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        {{-- PROFILE --}}
        <div class="tw-group tw-rounded-2xl tw-border tw-border-blue-100 tw-bg-white tw-p-6 tw-shadow-card tw-transition-all tw-duration-300 hover:-tw-translate-y-1 hover:tw-shadow-elevated">
            <div class="tw-mb-5 tw-flex tw-h-14 tw-w-14 tw-items-center tw-justify-center tw-rounded-2xl tw-bg-emerald-50 tw-text-xl tw-text-emerald-600">
                <i class="fas fa-user"></i>
            </div>

            <h5 class="tw-mb-2 tw-text-lg tw-font-extrabold tw-text-ess-text">My Profile</h5>

            <p class="tw-mb-5 tw-break-words tw-text-sm tw-leading-6 tw-text-ess-muted">
                {{ auth()->user()->name }}<br>
                {{ auth()->user()->email }}
            </p>

            <div class="tw-flex tw-flex-wrap tw-gap-2">
                <a href="{{ route('account.profile.edit') }}"
                   class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-xl tw-bg-ess-primary tw-px-3 tw-py-2.5 tw-text-xs tw-font-bold tw-text-white tw-no-underline tw-transition hover:tw-bg-ess-dark">
                    <i class="fas fa-user-edit"></i>
                    Manage Profile
                </a>

                <a href="{{ route('account.addresses.index') }}"
                   class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-xl tw-border tw-border-blue-200 tw-bg-blue-50 tw-px-3 tw-py-2.5 tw-text-xs tw-font-bold tw-text-ess-primary tw-no-underline tw-transition hover:tw-bg-blue-100">
                    <i class="fas fa-map-marker-alt"></i>
                    Addresses
                </a>
            </div>
        </div>

        {{-- SHOPPING --}}
        <div class="tw-group tw-rounded-2xl tw-border tw-border-blue-100 tw-bg-white tw-p-6 tw-shadow-card tw-transition-all tw-duration-300 hover:-tw-translate-y-1 hover:tw-shadow-elevated">
            <div class="tw-mb-5 tw-flex tw-h-14 tw-w-14 tw-items-center tw-justify-center tw-rounded-2xl tw-bg-amber-50 tw-text-xl tw-text-amber-600">
                <i class="fas fa-store"></i>
            </div>

            <h5 class="tw-mb-2 tw-text-lg tw-font-extrabold tw-text-ess-text">Continue Shopping</h5>

            <p class="tw-mb-5 tw-text-sm tw-leading-6 tw-text-ess-muted">
                Explore products from our marketplace sellers.
            </p>

            <a href="{{ route('home') }}"
               class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-xl tw-bg-ess-primary tw-px-4 tw-py-2.5 tw-text-sm tw-font-bold tw-text-white tw-no-underline tw-transition hover:tw-bg-ess-dark">
                Shop Now <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        {{-- LOGOUT --}}
        <div class="tw-group tw-rounded-2xl tw-border tw-border-red-100 tw-bg-white tw-p-6 tw-shadow-card tw-transition-all tw-duration-300 hover:-tw-translate-y-1 hover:tw-shadow-elevated">
            <div class="tw-mb-5 tw-flex tw-h-14 tw-w-14 tw-items-center tw-justify-center tw-rounded-2xl tw-bg-red-50 tw-text-xl tw-text-red-600">
                <i class="fas fa-sign-out-alt"></i>
            </div>

            <h5 class="tw-mb-2 tw-text-lg tw-font-extrabold tw-text-ess-text">Logout</h5>

            <p class="tw-mb-5 tw-text-sm tw-leading-6 tw-text-ess-muted">
                Securely sign out from your customer account.
            </p>

            <form action="{{ route('account.logout') }}" method="POST">
                @csrf

                <button type="submit"
                        class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-xl tw-border tw-border-red-200 tw-bg-red-50 tw-px-4 tw-py-2.5 tw-text-sm tw-font-bold tw-text-red-700 tw-transition hover:tw-bg-red-100">
                    Logout <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>

    </div>
</div>

@endsection
