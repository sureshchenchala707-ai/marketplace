
{{-- ESS MARKETPLACE STOREFRONT HEADER --}}

<div class="ess-storefront-header">

    {{-- ANNOUNCEMENT BAR --}}
    <div class="tw-bg-gradient-to-r tw-from-ess-dark tw-via-ess-primary tw-to-ess-dark tw-px-3 tw-py-2.5 tw-text-center tw-text-xs tw-font-semibold tw-tracking-wide tw-text-white sm:tw-text-sm">
        <i class="fas fa-sparkles tw-mr-2"></i>
        WELCOME TO ESS MARKETPLACE — DISCOVER PRODUCTS FROM TRUSTED SELLERS
    </div>

    {{-- MAIN NAVIGATION --}}
    <header class="tw-sticky tw-top-0 tw-z-50 tw-border-b tw-border-blue-100 tw-bg-white/95 tw-backdrop-blur-xl">

        <div class="tw-mx-auto tw-max-w-[1600px] tw-px-3 sm:tw-px-5 lg:tw-px-8">

            {{-- TOP ROW --}}
            <div class="tw-flex tw-min-h-[82px] tw-items-center tw-justify-between tw-gap-3 lg:tw-gap-6">

                {{-- LOGO --}}
                <a href="{{ route('home') }}"
                   class="tw-shrink-0 tw-whitespace-nowrap tw-text-xl tw-font-black tw-tracking-tight tw-text-ess-text tw-no-underline sm:tw-text-2xl">
                    ESS<span class="tw-text-ess-primary">.</span>MARKETPLACE
                </a>

                {{-- DESKTOP SEARCH --}}
                <form action="{{ route('home') }}"
                      method="GET"
                      class="tw-hidden tw-min-w-0 tw-flex-1 lg:tw-block">

                    <div class="tw-flex tw-items-center tw-gap-3 tw-rounded-full tw-border tw-border-transparent tw-bg-ess-surface tw-p-2 tw-transition focus-within:tw-border-ess-primary focus-within:tw-bg-white focus-within:tw-shadow-sm">

                        <i class="fas fa-search tw-ml-3 tw-text-lg tw-text-slate-400"></i>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search products, brands and categories..."
                            aria-label="Search products"
                            class="tw-min-w-0 tw-flex-1 tw-border-0 tw-bg-transparent tw-py-2 tw-text-sm tw-text-ess-text tw-outline-none placeholder:tw-text-slate-400"
                        >

                        <button type="submit"
                                class="tw-rounded-full tw-bg-ess-primary tw-px-7 tw-py-2.5 tw-text-sm tw-font-bold tw-text-white tw-transition hover:tw-bg-ess-dark">
                            Search
                        </button>
                    </div>
                </form>

                {{-- CUSTOMER ACTIONS --}}
                <div class="tw-flex tw-shrink-0 tw-items-center tw-gap-2 sm:tw-gap-3">

                    @auth
                        @if(auth()->user()->hasRole('customer'))

                            {{-- NOTIFICATIONS --}}
                            @include('notifications.partials.bell')

                            {{-- WISHLIST --}}
                            <a href="{{ route('account.wishlist.index') }}"
                               class="tw-relative tw-flex tw-h-10 tw-w-10 tw-items-center tw-justify-center tw-rounded-full tw-bg-ess-surface tw-text-ess-text tw-no-underline tw-transition hover:tw-bg-blue-100 hover:tw-text-ess-primary"
                               title="My Wishlist"
                               aria-label="My Wishlist">

                                <i class="fas fa-heart"></i>

                                <span class="tw-absolute -tw-right-1 -tw-top-1 tw-flex tw-h-5 tw-min-w-5 tw-items-center tw-justify-center tw-rounded-full tw-bg-red-500 tw-px-1 tw-text-[10px] tw-font-bold tw-text-white">
                                    {{ auth()->user()->wishlistItems()->count() }}
                                </span>
                            </a>

                            {{-- CART --}}
                            <a href="{{ route('account.cart.index') }}"
                               class="tw-relative tw-flex tw-h-10 tw-w-10 tw-items-center tw-justify-center tw-rounded-full tw-bg-ess-surface tw-text-ess-text tw-no-underline tw-transition hover:tw-bg-blue-100 hover:tw-text-ess-primary"
                               title="Shopping Cart"
                               aria-label="Shopping Cart">

                                <i class="fas fa-shopping-cart"></i>

                                <span class="tw-absolute -tw-right-1 -tw-top-1 tw-flex tw-h-5 tw-min-w-5 tw-items-center tw-justify-center tw-rounded-full tw-bg-ess-primary tw-px-1 tw-text-[10px] tw-font-bold tw-text-white">
                                    {{ auth()->user()->cartItems()->count() }}
                                </span>
                            </a>

                            {{-- ACCOUNT ICON --}}
                            <a href="{{ route('account.dashboard') }}"
                               class="tw-flex tw-h-10 tw-w-10 tw-items-center tw-justify-center tw-rounded-full tw-bg-ess-surface tw-text-ess-text tw-no-underline tw-transition hover:tw-bg-blue-100 hover:tw-text-ess-primary"
                               title="My Account"
                               aria-label="My Account">
                                <i class="fas fa-user"></i>
                            </a>

                        @else

                            <a href="{{ route('login') }}"
                               class="tw-flex tw-h-10 tw-w-10 tw-items-center tw-justify-center tw-rounded-full tw-bg-ess-surface tw-text-ess-text tw-no-underline">
                                <i class="fas fa-user"></i>
                            </a>

                        @endif
                    @else

                        <a href="{{ route('login') }}"
                           class="tw-flex tw-h-10 tw-w-10 tw-items-center tw-justify-center tw-rounded-full tw-bg-ess-surface tw-text-ess-text tw-no-underline hover:tw-bg-blue-100 hover:tw-text-ess-primary"
                           title="Login">
                            <i class="fas fa-user"></i>
                        </a>

                    @endauth

                    {{-- MY ACCOUNT BUTTON --}}
                    <a href="{{ auth()->check() && auth()->user()->hasRole('customer')
                                    ? route('account.dashboard')
                                    : route('login') }}"
                       class="tw-hidden tw-rounded-full tw-bg-blue-50 tw-px-5 tw-py-3 tw-text-sm tw-font-extrabold tw-text-ess-primary tw-no-underline tw-transition hover:tw-bg-blue-100 sm:tw-inline-flex">
                        My Account
                    </a>

                    {{-- SELL WITH US --}}
                    <a href="{{ route('vendor.login') }}"
                       class="tw-hidden tw-items-center tw-justify-center tw-rounded-full tw-bg-ess-primary tw-px-5 tw-py-3 tw-text-sm tw-font-extrabold tw-text-white tw-no-underline tw-shadow-sm tw-transition hover:tw-bg-ess-dark sm:tw-inline-flex">
                        <i class="fas fa-store tw-mr-2"></i>
                        Sell With Us
                    </a>

                </div>
            </div>

            {{-- MOBILE SEARCH --}}
            <div class="tw-pb-4 lg:tw-hidden">
                <form action="{{ route('home') }}" method="GET">
                    <div class="tw-flex tw-items-center tw-gap-2 tw-rounded-full tw-border tw-border-slate-200 tw-bg-ess-surface tw-p-1.5 focus-within:tw-border-ess-primary">

                        <i class="fas fa-search tw-ml-3 tw-text-slate-400"></i>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search products and brands..."
                            aria-label="Search products"
                            class="tw-min-w-0 tw-flex-1 tw-border-0 tw-bg-transparent tw-py-2 tw-text-sm tw-text-ess-text tw-outline-none"
                        >

                        <button type="submit"
                                class="tw-rounded-full tw-bg-ess-primary tw-px-4 tw-py-2 tw-text-xs tw-font-bold tw-text-white hover:tw-bg-ess-dark">
                            Search
                        </button>
                    </div>
                </form>
            </div>

            {{-- MOBILE SELL LINK --}}
            <div class="tw-flex tw-justify-end tw-pb-3 sm:tw-hidden">
                <a href="{{ route('vendor.login') }}"
                   class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-full tw-bg-blue-50 tw-px-4 tw-py-2 tw-text-xs tw-font-bold tw-text-ess-primary tw-no-underline">
                    <i class="fas fa-store"></i>
                    Sell With Us
                </a>
            </div>

        </div>
    </header>
</div>
