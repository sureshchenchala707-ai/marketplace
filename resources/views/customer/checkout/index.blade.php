
@extends('layouts.app')

@section('title', 'Checkout - ESS MARKETPLACE')

@section('content')

<div class="ess-customer tw-mx-auto tw-w-full tw-max-w-7xl tw-px-4 tw-py-6 sm:tw-px-6 lg:tw-px-8">

    {{-- HEADER --}}
    <div class="tw-mb-7">
        <p class="tw-mb-1 tw-text-xs tw-font-bold tw-uppercase tw-tracking-widest tw-text-ess-primary">
            ALMOST THERE
        </p>
        <h2 class="tw-mb-2 tw-text-2xl tw-font-extrabold tw-text-ess-text sm:tw-text-3xl">
            <i class="fas fa-lock tw-mr-2 tw-text-ess-primary"></i>
            Secure Checkout
        </h2>
        <p class="tw-mb-0 tw-text-sm tw-text-ess-muted">
            Choose your delivery address and review your order.
        </p>
    </div>

    {{-- VALIDATION / FLASH MESSAGES --}}
    @if($errors->any())
        <div class="tw-mb-5 tw-rounded-xl tw-border tw-border-red-200 tw-bg-red-50 tw-p-4 tw-text-sm tw-text-red-800">
            <div class="tw-mb-2 tw-font-bold">
                <i class="fas fa-exclamation-circle tw-mr-1"></i>
                Please correct the following:
            </div>
            <ul class="tw-mb-0 tw-list-inside tw-list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="tw-mb-5 tw-rounded-xl tw-border tw-border-green-200 tw-bg-green-50 tw-p-4 tw-text-sm tw-font-medium tw-text-green-800">
            <i class="fas fa-check-circle tw-mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <form method="POST"
          action="{{ route('account.checkout.place-order') }}"
          id="checkoutForm">
        @csrf

        <div class="tw-grid tw-grid-cols-1 tw-items-start tw-gap-6 lg:tw-grid-cols-5">

            {{-- LEFT: DELIVERY AND PAYMENT --}}
            <div class="tw-space-y-5 lg:tw-col-span-3">

                {{-- DELIVERY ADDRESS --}}
                <section class="tw-rounded-2xl tw-border tw-border-blue-100 tw-bg-white tw-p-5 tw-shadow-card sm:tw-p-6">

                    <div class="tw-mb-5 tw-flex tw-items-center tw-gap-3">
                        <div class="tw-flex tw-h-11 tw-w-11 tw-items-center tw-justify-center tw-rounded-xl tw-bg-blue-50 tw-text-lg tw-text-ess-primary">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h3 class="tw-mb-1 tw-text-lg tw-font-extrabold tw-text-ess-text">
                                Delivery Address
                            </h3>
                            <p class="tw-mb-0 tw-text-xs tw-text-ess-muted">
                                Where should we deliver your order?
                            </p>
                        </div>
                    </div>

                    <div class="tw-space-y-3">
                        @foreach($addresses as $address)
                            <label class="tw-flex tw-cursor-pointer tw-gap-3 tw-rounded-xl tw-border tw-border-slate-200 tw-p-4 tw-transition hover:tw-border-ess-primary hover:tw-bg-blue-50/40 has-[:checked]:tw-border-ess-primary has-[:checked]:tw-bg-blue-50">

                                <input
                                    type="radio"
                                    name="address_id"
                                    value="{{ $address->id }}"
                                    class="tw-mt-1 tw-h-4 tw-w-4 tw-shrink-0 tw-accent-ess-primary"
                                    @checked(old('address_id', $address->is_default ? $address->id : null) == $address->id)
                                    required
                                >

                                <div class="tw-min-w-0 tw-flex-1">
                                    <div class="tw-flex tw-flex-wrap tw-items-center tw-gap-2">
                                        <span class="tw-font-bold tw-text-ess-text">
                                            {{ $address->name }}
                                        </span>

                                        <span class="tw-rounded-md tw-bg-blue-50 tw-px-2 tw-py-1 tw-text-[10px] tw-font-bold tw-uppercase tw-text-ess-primary">
                                            {{ ucfirst($address->address_type) }}
                                        </span>

                                        @if($address->is_default)
                                            <span class="tw-rounded-md tw-bg-green-50 tw-px-2 tw-py-1 tw-text-[10px] tw-font-bold tw-text-green-700">
                                                Default
                                            </span>
                                        @endif
                                    </div>

                                    <p class="tw-mb-1 tw-mt-2 tw-text-xs tw-font-semibold tw-text-ess-muted">
                                        <i class="fas fa-phone tw-mr-1"></i>
                                        {{ $address->phone }}
                                    </p>

                                    <p class="tw-mb-0 tw-text-sm tw-leading-6 tw-text-slate-600">
                                        {{ $address->address_line1 }}
                                        @if($address->address_line2)
                                            , {{ $address->address_line2 }}
                                        @endif
                                        <br>
                                        {{ $address->city }}, {{ $address->state }} - {{ $address->pincode }}
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <a href="{{ route('account.addresses.create') }}"
                       class="tw-mt-4 tw-inline-flex tw-items-center tw-gap-2 tw-rounded-xl tw-border tw-border-blue-200 tw-bg-blue-50 tw-px-4 tw-py-2.5 tw-text-xs tw-font-bold tw-text-ess-primary tw-no-underline tw-transition hover:tw-bg-blue-100">
                        <i class="fas fa-plus"></i>
                        Add New Address
                    </a>
                </section>

                {{-- ORDER NOTE --}}
                <section class="tw-rounded-2xl tw-border tw-border-blue-100 tw-bg-white tw-p-5 tw-shadow-card sm:tw-p-6">
                    <div class="tw-mb-4 tw-flex tw-items-center tw-gap-3">
                        <div class="tw-flex tw-h-10 tw-w-10 tw-items-center tw-justify-center tw-rounded-xl tw-bg-blue-50 tw-text-ess-primary">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h3 class="tw-mb-0 tw-text-base tw-font-extrabold tw-text-ess-text">
                            Order Note <span class="tw-text-xs tw-font-normal tw-text-ess-muted">(Optional)</span>
                        </h3>
                    </div>

                    <textarea
                        name="customer_note"
                        rows="3"
                        maxlength="1000"
                        placeholder="Any delivery instructions?"
                        class="tw-w-full tw-rounded-xl tw-border tw-border-slate-200 tw-bg-white tw-p-3 tw-text-sm tw-text-ess-text focus:tw-border-ess-primary focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-100"
                    >{{ old('customer_note') }}</textarea>
                </section>

                {{-- PAYMENT METHOD --}}
                <section class="tw-rounded-2xl tw-border tw-border-blue-100 tw-bg-white tw-p-5 tw-shadow-card sm:tw-p-6">
                    <div class="tw-mb-5 tw-flex tw-items-center tw-gap-3">
                        <div class="tw-flex tw-h-11 tw-w-11 tw-items-center tw-justify-center tw-rounded-xl tw-bg-blue-50 tw-text-lg tw-text-ess-primary">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div>
                            <h3 class="tw-mb-1 tw-text-lg tw-font-extrabold tw-text-ess-text">
                                Select Payment Method
                            </h3>
                            <p class="tw-mb-0 tw-text-xs tw-text-ess-muted">
                                Choose how you want to pay.
                            </p>
                        </div>
                    </div>

                    <div class="tw-space-y-3">

                        <label class="tw-flex tw-cursor-pointer tw-gap-3 tw-rounded-xl tw-border tw-border-slate-200 tw-p-4 tw-transition hover:tw-border-ess-primary hover:tw-bg-blue-50/40 has-[:checked]:tw-border-ess-primary has-[:checked]:tw-bg-blue-50">
                            <input
                                type="radio"
                                name="payment_method"
                                value="razorpay"
                                class="tw-mt-1 tw-h-4 tw-w-4 tw-accent-ess-primary"
                                @checked(old('payment_method', 'cod') === 'razorpay')
                                required
                            >

                            <div class="tw-flex-1">
                                <div class="tw-flex tw-flex-wrap tw-items-center tw-gap-2">
                                    <span class="tw-font-bold tw-text-ess-text">Pay Online</span>
                                    <span class="tw-rounded-md tw-bg-ess-primary tw-px-2 tw-py-1 tw-text-[10px] tw-font-bold tw-text-white">
                                        RECOMMENDED
                                    </span>
                                </div>

                                <p class="tw-mb-1 tw-mt-1 tw-text-xs tw-text-ess-muted">
                                    UPI, Credit Card, Debit Card and Net Banking
                                </p>

                                <p class="tw-mb-0 tw-text-xs tw-font-semibold tw-text-green-600">
                                    <i class="fas fa-shield-alt tw-mr-1"></i>
                                    Secure payment through Razorpay
                                </p>
                            </div>
                        </label>

                        <label class="tw-flex tw-cursor-pointer tw-gap-3 tw-rounded-xl tw-border tw-border-slate-200 tw-p-4 tw-transition hover:tw-border-ess-primary hover:tw-bg-blue-50/40 has-[:checked]:tw-border-ess-primary has-[:checked]:tw-bg-blue-50">
                            <input
                                type="radio"
                                name="payment_method"
                                value="cod"
                                class="tw-mt-1 tw-h-4 tw-w-4 tw-accent-ess-primary"
                                @checked(old('payment_method', 'cod') === 'cod')
                            >

                            <div>
                                <span class="tw-font-bold tw-text-ess-text">
                                    Cash on Delivery
                                </span>
                                <p class="tw-mb-0 tw-mt-1 tw-text-xs tw-text-ess-muted">
                                    Pay when your order is delivered.
                                </p>
                            </div>
                        </label>

                    </div>
                </section>

            </div>

            {{-- RIGHT: ORDER SUMMARY --}}
            <aside class="lg:tw-sticky lg:tw-top-6 lg:tw-col-span-2">
                <div class="tw-overflow-hidden tw-rounded-2xl tw-border tw-border-blue-100 tw-bg-white tw-shadow-card">

                    <div class="tw-border-b tw-border-slate-100 tw-p-5">
                        <h3 class="tw-mb-1 tw-text-lg tw-font-extrabold tw-text-ess-text">
                            Order Summary
                        </h3>
                        <p class="tw-mb-0 tw-text-xs tw-text-ess-muted">
                            Review your items before placing your order.
                        </p>
                    </div>

                    <div class="tw-space-y-4 tw-p-5">

                        {{-- PRODUCTS --}}
                        <div class="tw-max-h-80 tw-space-y-4 tw-overflow-y-auto">
                            @foreach($cartItems as $item)

                                @php
                                    $variant = $item->variant;

                                    $unitPrice = $variant
                                        ? ($variant->sale_price ?? $variant->regular_price)
                                        : $item->product->current_price;

                                    $lineTotal = (float) $unitPrice * $item->quantity;

                                    $image = $item->product->images->firstWhere('is_primary', true)
                                        ?? $item->product->images->first();
                                @endphp

                                <div class="tw-flex tw-items-center tw-gap-3">
                                    @if($image)
                                        <img
                                            src="{{ asset('storage/' . $image->image_path) }}"
                                            alt="{{ $item->product->name }}"
                                            class="tw-h-16 tw-w-16 tw-shrink-0 tw-rounded-xl tw-bg-blue-50 tw-object-contain tw-p-1"
                                        >
                                    @else
                                        <div class="tw-flex tw-h-16 tw-w-16 tw-shrink-0 tw-items-center tw-justify-center tw-rounded-xl tw-bg-blue-50 tw-text-xl tw-text-slate-300">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif

                                    <div class="tw-min-w-0 tw-flex-1">
                                        <p class="tw-mb-1 tw-break-words tw-text-sm tw-font-bold tw-text-ess-text">
                                            {{ $item->product->name }}
                                        </p>

                                        @if($variant)
                                            <p class="tw-mb-1 tw-text-[11px] tw-text-ess-muted">
                                                {{ $variant->variant_name }}
                                            </p>
                                        @endif

                                        <p class="tw-mb-0 tw-text-xs tw-text-ess-muted">
                                            Qty: {{ $item->quantity }}
                                        </p>
                                    </div>

                                    <strong class="tw-shrink-0 tw-text-sm tw-font-bold tw-text-ess-text">
                                        ₹{{ number_format($lineTotal, 2) }}
                                    </strong>
                                </div>

                            @endforeach
                        </div>

                        <div class="tw-border-t tw-border-slate-100"></div>

                        {{-- COUPON --}}
                        <div>
                            <h4 class="tw-mb-3 tw-text-sm tw-font-extrabold tw-text-ess-text">
                                <i class="fas fa-ticket-alt tw-mr-2 tw-text-ess-primary"></i>
                                Apply Coupon
                            </h4>

                            <div id="couponMessage"
                                 class="tw-mb-2 tw-text-xs"
                                 role="status"
                                 aria-live="polite"></div>

                            @if($coupon)
                                <div id="appliedCoupon"
                                     class="tw-flex tw-items-center tw-justify-between tw-gap-3 tw-rounded-xl tw-border tw-border-green-200 tw-bg-green-50 tw-p-3">
                                    <div class="tw-min-w-0">
                                        <strong class="tw-text-sm tw-font-extrabold tw-text-green-700">
                                            {{ $coupon->code }}
                                        </strong>
                                        <p class="tw-mb-0 tw-mt-1 tw-text-[11px] tw-text-green-700">
                                            Coupon applied successfully
                                        </p>
                                    </div>

                                    <button type="button"
                                            class="tw-shrink-0 tw-rounded-lg tw-border tw-border-red-200 tw-bg-white tw-px-3 tw-py-2 tw-text-xs tw-font-bold tw-text-red-600 hover:tw-bg-red-50"
                                            id="removeCouponButton">
                                        Remove
                                    </button>
                                </div>

                                <div id="couponInputArea" class="tw-hidden">
                                    <div class="tw-flex tw-gap-2">
                                        <input type="text"
                                               id="couponCode"
                                               maxlength="50"
                                               placeholder="Enter coupon code"
                                               class="tw-min-w-0 tw-flex-1 tw-rounded-xl tw-border tw-border-slate-200 tw-px-3 tw-py-2.5 tw-text-sm focus:tw-border-ess-primary focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-100">

                                        <button type="button"
                                                class="tw-rounded-xl tw-bg-ess-primary tw-px-4 tw-py-2.5 tw-text-xs tw-font-bold tw-text-white hover:tw-bg-ess-dark"
                                                id="applyCouponButton">
                                            Apply
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div id="appliedCoupon" class="tw-hidden"></div>

                                <div id="couponInputArea">
                                    <div class="tw-flex tw-gap-2">
                                        <input type="text"
                                               id="couponCode"
                                               maxlength="50"
                                               placeholder="Enter coupon code"
                                               class="tw-min-w-0 tw-flex-1 tw-rounded-xl tw-border tw-border-slate-200 tw-px-3 tw-py-2.5 tw-text-sm focus:tw-border-ess-primary focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-100">

                                        <button type="button"
                                                class="tw-rounded-xl tw-bg-ess-primary tw-px-4 tw-py-2.5 tw-text-xs tw-font-bold tw-text-white hover:tw-bg-ess-dark"
                                                id="applyCouponButton">
                                            Apply
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="tw-border-t tw-border-slate-100"></div>

                        {{-- PRICE BREAKDOWN --}}
                        <div class="tw-flex tw-justify-between tw-gap-3 tw-text-sm">
                            <span class="tw-text-ess-muted">Subtotal</span>
                            <span id="checkoutSubtotal" class="tw-font-semibold tw-text-ess-text">
                                ₹{{ number_format($subtotal, 2) }}
                            </span>
                        </div>

                        <div class="tw-flex tw-justify-between tw-gap-3 tw-text-sm">
                            <span class="tw-text-ess-muted">Shipping</span>
                            <span class="tw-font-semibold tw-text-green-600">
                                {{ (float) $shipping > 0 ? '₹'.number_format($shipping, 2) : 'FREE' }}
                            </span>
                        </div>

                        <div id="discountRow"
                             class="{{ $couponDiscount > 0 ? 'tw-flex' : 'tw-hidden' }} tw-justify-between tw-gap-3 tw-text-sm">
                            <span class="tw-font-semibold tw-text-green-600">
                                Coupon Discount
                            </span>

                            <span class="tw-font-bold tw-text-green-600" id="couponDiscount">
                                -₹{{ number_format($couponDiscount, 2) }}
                            </span>
                        </div>

                        <div class="tw-border-t tw-border-dashed tw-border-slate-200"></div>

                        <div class="tw-flex tw-items-center tw-justify-between tw-gap-3">
                            <h4 class="tw-mb-0 tw-text-base tw-font-extrabold tw-text-ess-text">
                                Total
                            </h4>

                            <h4 class="tw-mb-0 tw-text-xl tw-font-extrabold tw-text-ess-primary"
                                id="checkoutTotal">
                                ₹{{ number_format($total, 2) }}
                            </h4>
                        </div>

                        {{-- PAYMENT INFO --}}
                        <div id="paymentMethodInfo"
                             class="tw-rounded-xl tw-border tw-border-blue-100 tw-bg-blue-50 tw-p-3 tw-text-xs tw-leading-5 tw-text-ess-primary">
                            <i class="fas fa-money-bill-wave tw-mr-2"></i>
                            Cash on Delivery selected.
                        </div>

                        {{-- PLACE ORDER --}}
                        <button type="submit"
                                class="tw-flex tw-w-full tw-items-center tw-justify-center tw-gap-2 tw-rounded-xl tw-bg-ess-primary tw-px-5 tw-py-4 tw-text-sm tw-font-extrabold tw-text-white tw-transition hover:tw-bg-ess-dark focus:tw-outline-none focus:tw-ring-4 focus:tw-ring-blue-100"
                                id="placeOrderButton">

                            <i class="fas fa-check-circle"></i>
                            <span id="placeOrderButtonText">Place COD Order</span>
                        </button>

                        <a href="{{ route('account.cart.index') }}"
                           class="tw-flex tw-w-full tw-items-center tw-justify-center tw-gap-2 tw-rounded-xl tw-border tw-border-slate-200 tw-bg-white tw-px-4 tw-py-3 tw-text-sm tw-font-bold tw-text-slate-600 tw-no-underline tw-transition hover:tw-bg-slate-50">
                            <i class="fas fa-arrow-left"></i>
                            Back to Cart
                        </a>

                        <p class="tw-mb-0 tw-text-center tw-text-[11px] tw-leading-5 tw-text-ess-muted">
                            <i class="fas fa-shield-alt tw-mr-1"></i>
                            Your checkout is protected by secure payment processing.
                        </p>

                    </div>
                </div>
            </aside>

        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // PAYMENT METHOD
    const paymentOptions = document.querySelectorAll('input[name="payment_method"]');
    const info = document.getElementById('paymentMethodInfo');
    const buttonText = document.getElementById('placeOrderButtonText');

    function updatePaymentMethod() {
        const selected = document.querySelector('input[name="payment_method"]:checked');

        if (!selected) return;

        if (selected.value === 'razorpay') {
            info.className = 'tw-rounded-xl tw-border tw-border-blue-200 tw-bg-blue-50 tw-p-3 tw-text-xs tw-leading-5 tw-text-ess-primary';
            info.innerHTML = '<i class="fas fa-shield-alt tw-mr-2"></i>You will be redirected to secure Razorpay payment.';
            buttonText.textContent = 'Continue to Online Payment';
        } else {
            info.className = 'tw-rounded-xl tw-border tw-border-blue-100 tw-bg-blue-50 tw-p-3 tw-text-xs tw-leading-5 tw-text-ess-primary';
            info.innerHTML = '<i class="fas fa-money-bill-wave tw-mr-2"></i>Cash on Delivery selected.';
            buttonText.textContent = 'Place COD Order';
        }
    }

    paymentOptions.forEach(option => {
        option.addEventListener('change', updatePaymentMethod);
    });

    updatePaymentMethod();

    // COUPON APPLICATION
    const applyButton = document.getElementById('applyCouponButton');
    const couponInput = document.getElementById('couponCode');
    const couponMessage = document.getElementById('couponMessage');
    const appliedCoupon = document.getElementById('appliedCoupon');
    const couponInputArea = document.getElementById('couponInputArea');

    const discountRow = document.getElementById('discountRow');
    const discountElement = document.getElementById('couponDiscount');
    const totalElement = document.getElementById('checkoutTotal');

    const applyUrl = @json(route('account.checkout.coupon.apply'));
    const removeUrl = @json(route('account.checkout.coupon.remove'));

    const csrfInput = document.querySelector('#checkoutForm input[name="_token"]');

    if (!csrfInput) return;

    const csrfToken = csrfInput.value;

    function showMessage(message, type = 'danger') {
        couponMessage.className = type === 'success'
            ? 'tw-mb-2 tw-text-xs tw-font-semibold tw-text-green-600'
            : 'tw-mb-2 tw-text-xs tw-font-semibold tw-text-red-600';

        couponMessage.textContent = message;
    }

    function updatePrice(discount) {
        const subtotal = Number(@json($subtotal));
        const shipping = Number(@json($shipping));

        const safeDiscount = Math.max(0, Math.min(
            Number(discount) || 0,
            subtotal + shipping
        ));

        const total = Math.max(0, subtotal + shipping - safeDiscount);

        if (safeDiscount > 0) {
            discountRow.classList.remove('tw-hidden');
            discountRow.classList.add('tw-flex');
            discountElement.textContent = '-₹' + safeDiscount.toFixed(2);
        } else {
            discountRow.classList.add('tw-hidden');
            discountRow.classList.remove('tw-flex');
            discountElement.textContent = '-₹0.00';
        }

        totalElement.textContent = '₹' + total.toFixed(2);
    }

    function showAppliedCoupon(code, discount) {
        appliedCoupon.className =
            'tw-flex tw-items-center tw-justify-between tw-gap-3 tw-rounded-xl tw-border tw-border-green-200 tw-bg-green-50 tw-p-3';

        appliedCoupon.replaceChildren();

        const details = document.createElement('div');
        details.className = 'tw-min-w-0';

        const strong = document.createElement('strong');
        strong.className = 'tw-text-sm tw-font-extrabold tw-text-green-700';
        strong.textContent = code;

        const message = document.createElement('p');
        message.className = 'tw-mb-0 tw-mt-1 tw-text-[11px] tw-text-green-700';
        message.textContent = 'Coupon applied successfully';

        details.append(strong, message);

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.id = 'removeCouponButton';
        remove.className = 'tw-shrink-0 tw-rounded-lg tw-border tw-border-red-200 tw-bg-white tw-px-3 tw-py-2 tw-text-xs tw-font-bold tw-text-red-600 hover:tw-bg-red-50';
        remove.textContent = 'Remove';
        remove.addEventListener('click', removeCoupon);

        appliedCoupon.append(details, remove);

        couponInputArea.classList.add('tw-hidden');
        updatePrice(discount);
    }

    async function applyCoupon() {
        if (!couponInput || !applyButton) return;

        const code = couponInput.value.trim().toUpperCase();

        if (!code) {
            showMessage('Please enter a coupon code.');
            return;
        }

        applyButton.disabled = true;
        applyButton.textContent = 'Applying...';

        try {
            const response = await fetch(applyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    coupon_code: code
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(
                    data.errors?.coupon_code?.[0] ||
                    data.message ||
                    'Unable to apply coupon.'
                );
            }

            showMessage(data.message, 'success');
            showAppliedCoupon(data.coupon, data.discount);

        } catch (error) {
            showMessage(error.message);
        } finally {
            applyButton.disabled = false;
            applyButton.textContent = 'Apply';
        }
    }

    async function removeCoupon() {
        const button = document.getElementById('removeCouponButton');

        if (button) {
            button.disabled = true;
            button.textContent = 'Removing...';
        }

        try {
            const response = await fetch(removeUrl, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Unable to remove coupon.');
            }

            appliedCoupon.classList.add('tw-hidden');
            couponInputArea.classList.remove('tw-hidden');

            if (couponInput) {
                couponInput.value = '';
            }

            updatePrice(0);
            showMessage(data.message, 'success');

        } catch (error) {
            showMessage(error.message);

            if (button) {
                button.disabled = false;
                button.textContent = 'Remove';
            }
        }
    }

    if (applyButton) {
        applyButton.addEventListener('click', applyCoupon);
    }

    if (couponInput) {
        couponInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                applyCoupon();
            }
        });
    }

    const initialRemoveButton = document.getElementById('removeCouponButton');

    if (initialRemoveButton) {
        initialRemoveButton.addEventListener('click', removeCoupon);
    }

});
</script>

@endsection
