
@extends('layouts.app')

@section('title', 'Razorpay Payment - ESS MARKETPLACE')

@section('content')

<div class="ess-customer tw-flex tw-min-h-[75vh] tw-items-center tw-justify-center tw-px-4 tw-py-10">

    <div class="tw-w-full tw-max-w-md tw-overflow-hidden tw-rounded-2xl tw-border tw-border-blue-100 tw-bg-white tw-shadow-card">

        {{-- HEADER --}}
        <div class="tw-bg-gradient-to-r tw-from-ess-dark tw-to-ess-accent tw-px-5 tw-py-5 tw-text-center tw-text-white">
            <div class="tw-mx-auto tw-mb-4 tw-flex tw-h-12 tw-w-12 tw-items-center tw-justify-center tw-rounded-2xl ttw-bg-white/15 tw-text-3xl">
                <i class="fas fa-shield-alt" style="margin-top: 10px"></i>
            </div>

            <h2 class="tw-mb-2 tw-text-xl tw-font-extrabold">
                Complete Your Payment
            </h2>

            <p class="tw-mb-0 tw-text-sm tw-text-white/80">
                Secure payment for your ESS MARKETPLACE order
            </p>
        </div>

        <div class="tw-p-5 sm:tw-p-6">

            {{-- ORDER DETAILS --}}
            <div class="tw-mb-4 tw-rounded-2xl tw-border tw-border-blue-100 tw-bg-blue-50 tw-p-4">
                <p class="tw-mb-1 tw-text-xs tw-font-semibold tw-uppercase tw-tracking-wide tw-text-ess-muted">
                    Order Number
                </p>

                <p class="tw-mb-0 tw-break-all tw-text-sm tw-font-extrabold tw-text-ess-text">
                    {{ $order->order_number }}
                </p>
            </div>

            {{-- AMOUNT --}}
            <div class="tw-mb-5 tw-rounded-xl tw-border tw-border-blue-100 tw-bg-white tw-p-4 tw-text-center">
                <p class="tw-mb-2 tw-text-xs tw-font-bold tw-uppercase tw-tracking-widest tw-text-ess-muted">
                    Amount to Pay
                </p>

                <h1 class="tw-mb-1 tw-text-3xl tw-font-extrabold tw-text-ess-primary ">
                    ₹{{ number_format($payment->amount, 2) }}
                </h1>

                <p class="tw-mb-0 tw-text-xs tw-text-ess-muted">
                    Indian Rupees (INR)
                </p>
            </div>

            {{-- PAYMENT BENEFITS --}}
            <div class="tw-mb-5 tw-space-y-3">
                <div class="tw-flex tw-items-center tw-gap-3">
                    <div class="tw-flex tw-h-9 tw-w-9 tw-items-center tw-justify-center tw-rounded-full tw-bg-green-50 tw-text-green-600">
                        <i class="fas fa-check"></i>
                    </div>
                    <span class="tw-text-sm tw-font-medium tw-text-ess-text">
                        Secure Razorpay checkout
                    </span>
                </div>

                <div class="tw-flex tw-items-center tw-gap-3">
                    <div class="tw-flex tw-h-9 tw-w-9 tw-items-center tw-justify-center tw-rounded-full tw-bg-green-50 tw-text-green-600">
                        <i class="fas fa-check"></i>
                    </div>
                    <span class="tw-text-sm tw-font-medium tw-text-ess-text">
                        UPI, Cards and Net Banking
                    </span>
                </div>

                <div class="tw-flex tw-items-center tw-gap-3">
                    <div class="tw-flex tw-h-9 tw-w-9 tw-items-center tw-justify-center tw-rounded-full tw-bg-green-50 tw-text-green-600">
                        <i class="fas fa-lock"></i>
                    </div>
                    <span class="tw-text-sm tw-font-medium tw-text-ess-text">
                        Protected payment verification
                    </span>
                </div>
            </div>

            {{-- PAYMENT FORM --}}
            <form id="razorpayVerifyForm"
                  method="POST"
                  action="{{ route('account.orders.razorpay.verify', $order) }}"
                  data-failure-url="{{ route('account.orders.razorpay.failure', $order) }}">

                @csrf

                <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
                <input type="hidden" name="razorpay_signature" id="razorpay_signature">

                <button type="button"
                        id="payNowButton"
                        class="tw-flex tw-w-full tw-items-center tw-justify-center tw-gap-2 tw-rounded-xl tw-bg-ess-primary tw-px-5 tw-py-3 tw-text-base tw-font-extrabold tw-text-white tw-transition hover:tw-bg-ess-dark focus:tw-outline-none focus:tw-ring-4 focus:tw-ring-blue-100 disabled:tw-cursor-not-allowed disabled:tw-opacity-60">

                    <i class="fas fa-lock"></i>
                    Pay ₹{{ number_format($payment->amount, 2) }}
                </button>
            </form>

            <a href="{{ route('account.orders.show', $order) }}"
               class="tw-mt-3 tw-flex tw-w-full tw-items-center tw-justify-center tw-gap-2 tw-rounded-xl tw-border tw-border-slate-200 tw-bg-white tw-px-5 tw-py-3 tw-text-sm tw-font-bold tw-text-slate-600 tw-no-underline tw-transition hover:tw-bg-slate-50">
                <i class="fas fa-arrow-left"></i>
                Cancel and Return to Order
            </a>

            <div class="tw-mt-5 tw-border-t tw-border-slate-100 tw-pt-5 tw-text-center">
                <p class="tw-mb-1 tw-text-xs tw-font-bold tw-text-ess-text">
                    ESS MARKETPLACE
                </p>
                <p class="tw-mb-0 tw-text-[11px] tw-text-ess-muted">
                    <i class="fas fa-shield-alt tw-mr-1" ></i>
                    Secure Checkout Experience
                </p>
            </div>

        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const payButton = document.getElementById('payNowButton');
    const verifyForm = document.getElementById('razorpayVerifyForm');

    if (!payButton || !verifyForm) return;

    const originalButtonHTML = payButton.innerHTML;

    payButton.addEventListener('click', function () {

        if (typeof Razorpay === 'undefined') {
            alert('Unable to load Razorpay checkout. Please check your internet connection.');
            return;
        }

        payButton.disabled = true;
        payButton.innerHTML =
            '<span class="tw-mr-2 tw-inline-block tw-animate-spin">⟳</span>Opening secure checkout...';

        const options = {
            key: @json($razorpayKey),
            amount: {{ (int) round($payment->amount * 100) }},
            currency: 'INR',
            name: 'ESS MARKETPLACE',
            description: 'Order {{ $order->order_number }}',
            order_id: @json($razorpayOrderId),

            prefill: {
                name: @json($order->shipping_name),
                contact: @json($order->shipping_phone),
                email: @json(auth()->user()->email)
            },

            theme: {
                color: '#0645e8'
            },

            handler: function (response) {

                document.getElementById('razorpay_payment_id').value =
                    response.razorpay_payment_id;

                document.getElementById('razorpay_order_id').value =
                    response.razorpay_order_id;

                document.getElementById('razorpay_signature').value =
                    response.razorpay_signature;

                payButton.disabled = true;
                payButton.textContent = 'Verifying payment securely...';

                verifyForm.submit();
            },

            modal: {
                ondismiss: function () {
                    payButton.disabled = false;
                    payButton.innerHTML = originalButtonHTML;
                }
            }
        };

        try {
            const razorpay = new Razorpay(options);

            razorpay.on('payment.failed', function (response) {

                const metadata = response.error?.metadata || {};

                if (!metadata.payment_id || !metadata.order_id) {
                    payButton.disabled = false;
                    payButton.innerHTML =
                        '<i class="fas fa-redo tw-mr-2"></i>Try Payment Again';

                    alert(
                        response.error?.description ||
                        'Payment failed. Please check your order status.'
                    );

                    return;
                }

                document.getElementById('razorpay_payment_id').value =
                    metadata.payment_id;

                document.getElementById('razorpay_order_id').value =
                    metadata.order_id;

                payButton.disabled = true;
                payButton.textContent = 'Verifying failed payment...';

                verifyForm.action = verifyForm.dataset.failureUrl;
                verifyForm.submit();
            });

            razorpay.open();

        } catch (error) {
            payButton.disabled = false;
            payButton.innerHTML =
                '<i class="fas fa-redo tw-mr-2"></i>Try Again';

            alert('Unable to open Razorpay checkout. Please try again.');
        }
    });

});
</script>

@endsection
