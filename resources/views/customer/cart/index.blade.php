
@extends('layouts.app')

@section('title', 'Shopping Cart | ESS MARKETPLACE')

@section('content')

<div class="ess-customer tw-mx-auto tw-w-full tw-max-w-7xl tw-px-4 tw-py-6 sm:tw-px-6 lg:tw-px-8">

    {{-- PAGE HEADER --}}
    <div class="tw-mb-7 tw-flex tw-flex-wrap tw-items-center tw-justify-between tw-gap-4">
        <div>
            <p class="tw-mb-1 tw-text-xs tw-font-bold tw-uppercase tw-tracking-widest tw-text-ess-primary">
                YOUR SELECTION
            </p>
            <h2 class="tw-mb-1 tw-text-2xl tw-font-extrabold tw-text-ess-text sm:tw-text-3xl">
                Shopping Cart
            </h2>
            <p class="tw-mb-0 tw-text-sm tw-text-ess-muted">
                Review your selected products before checkout.
            </p>
        </div>

        <a href="{{ route('home') }}"
           class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-xl tw-border tw-border-blue-200 tw-bg-white tw-px-4 tw-py-3 tw-text-sm tw-font-bold tw-text-ess-primary tw-no-underline tw-transition hover:tw-bg-blue-50">
            <i class="fas fa-arrow-left"></i>
            Continue Shopping
        </a>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="tw-mb-5 tw-flex tw-items-center tw-gap-3 tw-rounded-xl tw-border tw-border-green-200 tw-bg-green-50 tw-p-4 tw-text-sm tw-font-medium tw-text-green-800">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="tw-mb-5 tw-flex tw-items-center tw-gap-3 tw-rounded-xl tw-border tw-border-red-200 tw-bg-red-50 tw-p-4 tw-text-sm tw-font-medium tw-text-red-800">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="tw-mb-5 tw-rounded-xl tw-border tw-border-red-200 tw-bg-red-50 tw-p-4 tw-text-sm tw-text-red-800">
            <div class="tw-mb-2 tw-font-bold">Please check the following:</div>
            @foreach($errors->all() as $error)
                <div class="tw-mt-1">{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- EMPTY CART --}}
    @if($cartItems->isEmpty())

        <div class="tw-rounded-3xl tw-border tw-border-blue-100 tw-bg-white tw-px-5 tw-py-16 tw-text-center tw-shadow-card">
            <div class="tw-mx-auto tw-mb-6 tw-flex tw-h-24 tw-w-24 tw-items-center tw-justify-center tw-rounded-full tw-bg-blue-50 tw-text-4xl tw-text-ess-primary">
                <i class="fas fa-shopping-cart"></i>
            </div>

            <h3 class="tw-mb-2 tw-text-xl tw-font-extrabold tw-text-ess-text">
                Your cart is empty
            </h3>

            <p class="tw-mx-auto tw-mb-7 tw-max-w-md tw-text-sm tw-leading-6 tw-text-ess-muted">
                Discover something you love and add it to your cart. Your next favourite purchase might be waiting!
            </p>

            <a href="{{ route('home') }}"
               class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-xl tw-bg-ess-primary tw-px-6 tw-py-3 tw-text-sm tw-font-bold tw-text-white tw-no-underline tw-transition hover:tw-bg-ess-dark">
                Explore Products
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

    @else

        {{-- BOGO OFFER BANNER --}}
        @if($bogoOffer && $bogoDiscount > 0)
            <div class="tw-mb-6 tw-flex tw-flex-wrap tw-items-center tw-justify-between tw-gap-4 tw-rounded-2xl tw-border tw-border-blue-200 tw-bg-gradient-to-r tw-from-blue-50 tw-to-indigo-50 tw-p-5 sm:tw-p-6">
                <div class="tw-flex tw-min-w-0 tw-items-start tw-gap-4">
                    <div class="tw-flex h-12 tw-w-12 tw-shrink-0 tw-items-center tw-justify-center tw-rounded-xl tw-bg-white tw-text-2xl tw-text-ess-primary tw-shadow-sm">
                        <i class="fas fa-gift"></i>
                    </div>

                    <div>
                        <span class="tw-inline-flex tw-rounded-md tw-bg-ess-primary tw-px-2.5 tw-py-1 tw-text-[10px] tw-font-extrabold tw-tracking-wider tw-text-white">
                            BUY 1 GET 1 FREE
                        </span>

                        <h4 class="tw-mb-1 tw-mt-2 tw-text-base tw-font-extrabold tw-text-ess-text">
                            {{ $bogoOffer->name }}
                        </h4>

                        <p class="tw-mb-0 tw-text-xs tw-leading-5 tw-text-ess-muted">
                            Add qualifying quantities of the same product and get the cheapest eligible unit free.
                        </p>
                    </div>
                </div>

                <div class="tw-min-w-28 tw-rounded-xl tw-bg-white tw-px-4 tw-py-3 tw-text-right tw-shadow-sm">
                    <span class="tw-block tw-text-xs tw-font-medium tw-text-ess-muted">You save</span>
                    <strong class="tw-text-xl tw-font-extrabold tw-text-green-600">
                        ₹{{ number_format($bogoDiscount, 2) }}
                    </strong>
                </div>
            </div>
        @endif

        <div class="tw-grid tw-grid-cols-1 tw-items-start tw-gap-6 lg:tw-grid-cols-3">

            {{-- CART ITEMS --}}
            <div class="tw-space-y-4 lg:tw-col-span-2">

                <div class="tw-flex tw-items-center tw-justify-between">
                    <h3 class="tw-mb-0 tw-text-lg tw-font-extrabold tw-text-ess-text">
                        Cart Items
                    </h3>
                    <span class="tw-rounded-full tw-bg-blue-50 tw-px-3 tw-py-1 tw-text-xs tw-font-bold tw-text-ess-primary">
                        {{ $cartItems->sum('quantity') }} items
                    </span>
                </div>

                @foreach($cartItems as $item)

                    @php
                        $product = $item->product;

                        $image = $product
                            ? (
                                $product->images->firstWhere('is_primary', true)
                                ?? $product->images->first()
                            )
                            : null;

                        $itemBogoDiscount = (float) ($item->bogo_discount ?? 0);

                        $itemFinalTotal = (float) (
                            $item->discounted_line_total ?? $item->line_total
                        );
                    @endphp

                    <div class="tw-overflow-hidden tw-rounded-2xl tw-border tw-border-blue-100 tw-bg-white tw-p-4 tw-shadow-card sm:tw-p-5">

                        <div class="tw-grid tw-grid-cols-1 tw-gap-4 sm:tw-grid-cols-[112px_minmax(0,1fr)]">

                            {{-- PRODUCT IMAGE --}}
                            <div class="tw-flex tw-items-start tw-justify-center">
                                @if($image)
                                    <img
                                        class="tw-h-28 tw-w-28 tw-rounded-xl tw-bg-blue-50 tw-object-contain tw-p-2"
                                        src="{{ asset('storage/'.$image->image_path) }}"
                                        alt="{{ $product->name }}"
                                    >
                                @else
                                    <div class="tw-flex tw-h-28 tw-w-28 tw-items-center tw-justify-center tw-rounded-xl tw-bg-blue-50 tw-text-3xl tw-text-slate-300">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- PRODUCT INFORMATION --}}
                            <div class="tw-min-w-0">

                                <div class="tw-flex tw-flex-wrap tw-items-start tw-justify-between tw-gap-3">
                                    <div class="tw-min-w-0">
                                        <h4 class="tw-mb-1 tw-break-words tw-text-base tw-font-extrabold tw-text-ess-text">
                                            {{ $product?->name ?? 'Unavailable product' }}
                                        </h4>

                                        @if($item->variant)
                                            <p class="tw-mb-1 tw-text-xs tw-font-semibold tw-text-ess-primary">
                                                Variant: {{ $item->variant->variant_name }}
                                            </p>
                                        @endif

                                        <p class="tw-mb-0 tw-text-xs tw-text-ess-muted">
                                            {{ $product?->vendor?->shop_name }}
                                        </p>
                                    </div>

                                    <div class="tw-text-right">
                                        <div class="tw-text-base tw-font-extrabold tw-text-ess-primary">
                                            ₹{{ number_format($item->unit_price, 2) }}
                                        </div>
                                        <span class="tw-text-[11px] tw-text-ess-muted">per unit</span>
                                    </div>
                                </div>

                                {{-- BOGO ITEM SAVING --}}
                                @if($itemBogoDiscount > 0)
                                    <div class="tw-mt-3 tw-rounded-xl tw-border tw-border-green-100 tw-bg-green-50 tw-p-3">
                                        <div class="tw-flex tw-flex-wrap tw-items-center tw-justify-between tw-gap-2">
                                            <span class="tw-inline-flex tw-items-center tw-gap-2 tw-text-[11px] tw-font-extrabold tw-tracking-wide tw-text-green-700">
                                                <i class="fas fa-gift"></i>
                                                BOGO APPLIED
                                            </span>

                                            <span class="tw-text-xs tw-font-bold tw-text-green-700">
                                                You save ₹{{ number_format($itemBogoDiscount, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                {{-- QUANTITY AND LINE TOTAL --}}
                                <div class="tw-mt-4 tw-flex tw-flex-wrap tw-items-end tw-justify-between tw-gap-4">

                                    <form
                                        action="{{ route('account.cart.update', $item) }}"
                                        method="POST"
                                        class="tw-flex tw-flex-col tw-gap-1.5"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <label class="tw-text-xs tw-font-bold tw-text-ess-text">
                                            Quantity
                                        </label>

                                        <div class="tw-flex tw-items-center tw-gap-2">
                                            <input
                                                type="number"
                                                name="quantity"
                                                value="{{ $item->quantity }}"
                                                min="1"
                                                max="20"
                                                class="tw-h-10 tw-w-20 tw-rounded-lg tw-border tw-border-slate-200 tw-bg-white tw-px-3 tw-text-sm tw-font-semibold tw-text-ess-text focus:tw-border-ess-primary focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-100"
                                                required
                                            >

                                            <button
                                                type="submit"
                                                class="tw-inline-flex tw-h-10 tw-items-center tw-gap-2 tw-rounded-lg tw-border tw-border-blue-200 tw-bg-blue-50 tw-px-3 tw-text-xs tw-font-bold tw-text-ess-primary tw-transition hover:tw-bg-blue-100"
                                            >
                                                <i class="fas fa-sync-alt"></i>
                                                Update
                                            </button>
                                        </div>
                                    </form>

                                    <div class="tw-text-right">
                                        <span class="tw-mb-1 tw-block tw-text-[11px] tw-font-semibold tw-uppercase tw-tracking-wide tw-text-ess-muted">
                                            Item Total
                                        </span>

                                        @if($itemBogoDiscount > 0)
                                            <div class="tw-text-xs tw-text-slate-400 tw-line-through">
                                                ₹{{ number_format($item->line_total, 2) }}
                                            </div>

                                            <div class="tw-text-lg tw-font-extrabold tw-text-green-600">
                                                ₹{{ number_format($itemFinalTotal, 2) }}
                                            </div>
                                        @else
                                            <div class="tw-text-lg tw-font-extrabold tw-text-ess-text">
                                                ₹{{ number_format($item->line_total, 2) }}
                                            </div>
                                        @endif

                                        <form
                                            action="{{ route('account.cart.destroy', $item) }}"
                                            method="POST"
                                            class="tw-mt-2"
                                            onsubmit="return confirm('Remove this product from cart?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="tw-inline-flex tw-items-center tw-gap-1.5 tw-rounded-lg tw-px-2 py-1 tw-text-xs tw-font-semibold tw-text-red-600 tw-transition hover:tw-bg-red-50"
                                            >
                                                <i class="fas fa-trash-alt"></i>
                                                Remove
                                            </button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                @endforeach

                {{-- CLEAR CART --}}
                <div class="tw-flex tw-justify-end">
                    <form
                        action="{{ route('account.cart.clear') }}"
                        method="POST"
                        onsubmit="return confirm('Clear your entire cart?')"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-lg tw-px-3 tw-py-2 tw-text-xs tw-font-bold tw-text-red-600 tw-transition hover:tw-bg-red-50"
                        >
                            <i class="fas fa-trash"></i>
                            Clear Cart
                        </button>
                    </form>
                </div>

            </div>

            {{-- ORDER SUMMARY --}}
            <aside class="lg:tw-sticky lg:tw-top-6">
                <div class="tw-overflow-hidden tw-rounded-2xl tw-border tw-border-blue-100 tw-bg-white tw-shadow-card">

                    <div class="tw-border-b tw-border-slate-100 tw-p-5">
                        <h3 class="tw-mb-0 tw-text-lg tw-font-extrabold tw-text-ess-text">
                            Order Summary
                        </h3>
                    </div>

                    <div class="tw-space-y-4 tw-p-5">

                        <div class="tw-flex tw-justify-between tw-gap-3 tw-text-sm">
                            <span class="tw-text-ess-muted">
                                Items ({{ $cartItems->sum('quantity') }})
                            </span>
                            <strong class="tw-text-ess-text">
                                ₹{{ number_format($subtotal, 2) }}
                            </strong>
                        </div>

                        @if($bogoDiscount > 0)
                            <div class="tw-flex tw-justify-between tw-gap-3 tw-text-sm">
                                <span class="tw-font-semibold tw-text-green-600">
                                    <i class="fas fa-gift tw-mr-1"></i>
                                    BOGO Discount
                                </span>
                                <strong class="tw-text-green-600">
                                    -₹{{ number_format($bogoDiscount, 2) }}
                                </strong>
                            </div>
                        @endif

                        <div class="tw-flex tw-justify-between tw-gap-3 tw-text-sm">
                            <span class="tw-text-ess-muted">Shipping</span>
                            <span class="tw-text-right tw-text-xs tw-text-ess-muted">
                                Calculated at checkout
                            </span>
                        </div>

                        <div class="tw-border-t tw-border-dashed tw-border-slate-200"></div>

                        <div class="tw-flex tw-items-center tw-justify-between tw-gap-3">
                            <strong class="tw-text-sm tw-font-extrabold tw-text-ess-text">
                                Payable Subtotal
                            </strong>

                            <strong class="tw-text-xl tw-font-extrabold tw-text-ess-primary">
                                ₹{{ number_format($payableSubtotal, 2) }}
                            </strong>
                        </div>

                        @if($bogoDiscount > 0)
                            <div class="tw-flex tw-items-center tw-gap-2 tw-rounded-xl tw-bg-green-50 tw-p-3 tw-text-xs tw-font-semibold tw-text-green-700">
                                <i class="fas fa-check-circle"></i>
                                Your BOGO savings have been applied!
                            </div>
                        @endif

                        <a
                            href="{{ route('account.checkout.index') }}"
                            class="tw-flex tw-w-full tw-items-center tw-justify-center tw-gap-2 tw-rounded-xl tw-bg-ess-primary tw-px-5 tw-py-3.5 tw-text-sm tw-font-extrabold tw-text-white tw-no-underline tw-shadow-sm tw-transition hover:tw-bg-ess-dark"
                        >
                            <i class="fas fa-lock"></i>
                            Proceed to Checkout
                        </a>

                        <p class="tw-mb-0 tw-text-center tw-text-[11px] tw-leading-5 tw-text-ess-muted">
                            Final shipping, taxes and eligible coupon discounts will be calculated during checkout.
                        </p>

                    </div>
                </div>

                <div class="tw-mt-4 tw-flex tw-items-center tw-justify-center tw-gap-2 tw-rounded-xl tw-bg-blue-50 tw-p-3 tw-text-xs tw-font-semibold tw-text-ess-primary">
                    <i class="fas fa-shield-alt"></i>
                    Secure checkout experience
                </div>
            </aside>

        </div>

    @endif

</div>

@endsection
