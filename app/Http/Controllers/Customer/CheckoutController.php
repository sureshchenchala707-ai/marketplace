<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Offer;
use App\Models\OfferUsage;
use App\Models\Payment;
use App\Services\BogoOfferService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Razorpay\Api\Api;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Services\OrderFinalizationService;

class CheckoutController extends Controller
{
    private const COUPON_SESSION_KEY = 'checkout_coupon_code';

    /**
     * Display the checkout page.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $cartItems = $user->cartItems()
            ->with([
                'product.vendor',
                'product.category',
                'product.images',
                'variant',
            ])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('account.cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $addresses = $user->addresses()
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        if ($addresses->isEmpty()) {
            return redirect()
                ->route('account.addresses.create')
                ->with('error', 'Please add a delivery address before checkout.');
        }

        $subtotal = $this->cartSubtotal($cartItems);

        /*
         * Calculate the marketplace-wide BOGO offer.
         */
        $bogoService = app(BogoOfferService::class);
        $bogoOffer = $bogoService->activeOffer();

        // Respect the offer's minimum order amount.
        if (
            $bogoOffer &&
            $subtotal < (float) $bogoOffer->minimum_order_amount
        ) {
            $bogoOffer = null;
        }

        $bogoQuote = $bogoService->calculate(
            $cartItems->map(function ($item) {
                $price = $item->variant
                    ? ($item->variant->sale_price ?? $item->variant->regular_price)
                    : $item->product->current_price;

                return [
                    'product_id' => $item->product_id,
                    'quantity' => (int) $item->quantity,
                    'unit_price' => (float) $price,
                ];
            }),
            $bogoOffer
        );

        $bogoOffer = $bogoQuote['offer'];
        $bogoDiscount = (float) $bogoQuote['discount'];

        /*
         * Calculate any coupon already applied to this checkout session.
         */
        $coupon = null;
        $couponDiscount = 0;

        $couponCode = $request->session()->get(self::COUPON_SESSION_KEY);

        // A non-stackable BOGO offer takes priority over coupons.
        if ($bogoOffer && !$bogoOffer->is_stackable) {
            $request->session()->forget(self::COUPON_SESSION_KEY);
            $couponCode = null;
        }

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();

            if ($coupon) {
                try {
                    $couponQuote = $this->calculateCouponDiscount(
                        $coupon,
                        $cartItems,
                        $subtotal,
                        $user->id
                    );

                    $couponDiscount = (float) $couponQuote['discount'];
                } catch (ValidationException $e) {
                    $request->session()->forget(self::COUPON_SESSION_KEY);
                    $coupon = null;
                    $couponDiscount = 0;
                }
            } else {
                $request->session()->forget(self::COUPON_SESSION_KEY);
            }
        }

        $shipping = 0;
        $tax = 0;

        $total = max(
            0,
            round(
                $subtotal + $shipping + $tax - $bogoDiscount - $couponDiscount,
                2
            )
        );

        return view('customer.checkout.index', compact(
            'cartItems',
            'addresses',
            'subtotal',
            'shipping',
            'tax',
            'total',
            'coupon',
            'couponDiscount',
            'bogoOffer',
            'bogoDiscount',
            'bogoQuote'
        ));
    }

    /**
     * Apply a coupon to the current checkout session.
     */
    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'coupon_code' => ['required', 'string', 'max:50'],
        ]);

        $user = $request->user();

        $cartItems = $user->cartItems()
            ->with([
                'product.vendor',
                'product.category',
                'variant',
            ])
            ->get();

        if ($cartItems->isEmpty()) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Your cart is empty.',
            ]);
        }

        $subtotal = $this->cartSubtotal($cartItems);

        /*
         * Check whether a non-stackable BOGO offer is active.
         */
        $bogoOffer = app(BogoOfferService::class)->activeOffer();

        if (
            $bogoOffer &&
            $subtotal >= (float) $bogoOffer->minimum_order_amount &&
            !$bogoOffer->is_stackable
        ) {
            throw ValidationException::withMessages([
                'coupon_code' => 'A Buy 1 Get 1 offer is already active. This offer cannot be combined with coupons.',
            ]);
        }

        $code = strtoupper(trim($validated['coupon_code']));

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Invalid coupon code.',
            ]);
        }

        $quote = $this->calculateCouponDiscount(
            $coupon,
            $cartItems,
            $subtotal,
            $user->id
        );

        $request->session()->put(self::COUPON_SESSION_KEY, $coupon->code);

        /*
         * Include any stackable BOGO discount in the displayed total.
         */
        $bogoDiscount = 0;

        if ($bogoOffer) {
            $bogoQuote = app(BogoOfferService::class)->calculate(
                $cartItems->map(function ($item) {
                    $price = $item->variant
                        ? ($item->variant->sale_price ?? $item->variant->regular_price)
                        : $item->product->current_price;

                    return [
                        'product_id' => $item->product_id,
                        'quantity' => (int) $item->quantity,
                        'unit_price' => (float) $price,
                    ];
                }),
                $bogoOffer
            );

            $bogoDiscount = (float) $bogoQuote['discount'];
        }

        $total = max(
            0,
            round($subtotal - $bogoDiscount - $quote['discount'], 2)
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Coupon applied successfully.',
                'coupon' => $coupon->code,
                'discount' => $quote['discount'],
                'bogo_discount' => $bogoDiscount,
                'subtotal' => $subtotal,
                'total' => $total,
            ]);
        }

        return back()->with('success', 'Coupon applied successfully.');
    }

    /**
     * Remove the applied coupon from checkout.
     */
    public function removeCoupon(Request $request)
    {
        $request->session()->forget(self::COUPON_SESSION_KEY);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Coupon removed successfully.',
            ]);
        }

        return back()->with('success', 'Coupon removed successfully.');
    }

    /**
     * Place the order and record coupon and BOGO usage transactionally.
     */
    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'address_id' => [
                'required',
                'integer',
                'exists:customer_addresses,id',
            ],
            'customer_note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cod,razorpay'],
        ]);

        $user = $request->user();
        $paymentMethod = $validated['payment_method'];

        $couponCode = $request->session()->get(self::COUPON_SESSION_KEY);

        $order = DB::transaction(function () use (
            $user,
            $validated,
            $paymentMethod,
            $couponCode
    ) {
            /*
             * Validate the selected delivery address.
             */
            $address = $user->addresses()
                ->whereKey($validated['address_id'])
                ->first();

            if (!$address) {
                throw ValidationException::withMessages([
                    'address_id' => 'Please select a valid delivery address.',
                ]);
            }

            /*
             * Lock the customer's cart rows.
             */
            $cartItems = CartItem::where('user_id', $user->id)
                ->with([
                    'product.vendor',
                    'product.category',
                    'product.images',
                    'variant',
                ])
                ->lockForUpdate()
                ->get();

            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Your cart is empty.',
                ]);
            }

            /*
             * Validate products, variants, stock and prices.
             */
            $preparedItems = [];
            $subtotal = 0;

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product()
                    ->lockForUpdate()
                    ->first();

                if (
                    !$product ||
                    !$product->is_active ||
                    $product->status !== 'approved' ||
                    !$product->vendor ||
                    $product->vendor->status !== 'approved' ||
                    !$product->category ||
                    !$product->category->is_active
                ) {
                    throw ValidationException::withMessages([
                        'cart' => 'One or more cart products are no longer available.',
                    ]);
                }

                $activeVariants = $product->variants()
                    ->where('is_active', true)
                    ->exists();

                $variant = null;

                if ($activeVariants) {
                    if (!$cartItem->product_variant_id) {
                        throw ValidationException::withMessages([
                            'cart' => "Please select a variant for {$product->name}.",
                        ]);
                    }

                    $variant = $product->variants()
                        ->whereKey($cartItem->product_variant_id)
                        ->where('is_active', true)
                        ->lockForUpdate()
                        ->first();

                    if (!$variant) {
                        throw ValidationException::withMessages([
                            'cart' => "Selected variant for {$product->name} is unavailable.",
                        ]);
                    }

                    $stock = (int) $variant->stock_quantity;

                    $unitPrice = (float) (
                        $variant->sale_price ?? $variant->regular_price
                    );
                } else {
                    if ($cartItem->product_variant_id) {
                        throw ValidationException::withMessages([
                            'cart' => "Selected variant for {$product->name} is unavailable.",
                        ]);
                    }

                    $stock = (int) $product->stock_quantity;
                    $unitPrice = (float) $product->current_price;
                }

                if ((int) $cartItem->quantity > $stock) {
                    throw ValidationException::withMessages([
                        'cart' => "Insufficient stock for {$product->name}. Available: {$stock}.",
                    ]);
                }

                if ($unitPrice <= 0) {
                    throw ValidationException::withMessages([
                        'cart' => "Invalid price for {$product->name}.",
                    ]);
                }

                $lineTotal = round(
                    $unitPrice * (int) $cartItem->quantity,
                    2
                );

                $subtotal += $lineTotal;

                $preparedItems[] = [
                    'cart_item' => $cartItem,
                    'product' => $product,
                    'variant' => $variant,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            $subtotal = round($subtotal, 2);
            $shipping = 0;
            $tax = 0;



            /*
             * Lock and validate the active marketplace-wide BOGO offer.
             */
            $bogoOffer = Offer::query()
                ->where('offer_type', 'bogo')
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('starts_at')
                        ->orWhere('starts_at', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('usage_limit')
                        ->orWhereColumn('used_count', '<', 'usage_limit');
                })
                ->whereDoesntHave('products')
                ->whereDoesntHave('categories')
                ->whereDoesntHave('vendors')
                ->orderByDesc('priority')
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (
                $bogoOffer &&
                $subtotal < (float) $bogoOffer->minimum_order_amount
            ) {
                $bogoOffer = null;
            }

            /*
             * Calculate BOGO discount using the same product ID.
             * Different variants of the same product are grouped together.
             */
            $bogoQuote = app(BogoOfferService::class)->calculate(
                collect($preparedItems)->map(function ($item) {
                    return [
                        'product_id' => $item['product']->id,
                        'quantity' => (int) $item['cart_item']->quantity,
                        'unit_price' => (float) $item['unit_price'],
                    ];
                }),
                $bogoOffer
            );

            $bogoOffer = $bogoQuote['offer'];
            $bogoDiscount = (float) $bogoQuote['discount'];
            $bogoItemDiscounts = $bogoQuote['item_discounts'];

            /*
             * Validate and calculate coupon discount, if one was applied.
             */
            $coupon = null;
            $couponQuote = null;
            $couponDiscount = 0;

            if ($couponCode) {
                $coupon = Coupon::where('code', $couponCode)
                    ->lockForUpdate()
                    ->first();

                if (!$coupon) {
                    throw ValidationException::withMessages([
                        'coupon_code' => 'The applied coupon is no longer available.',
                    ]);
                }

                if ($bogoOffer && !$bogoOffer->is_stackable) {
                    throw ValidationException::withMessages([
                        'coupon_code' => 'A Buy 1 Get 1 offer cannot be combined with coupons. Remove your coupon to continue.',
                    ]);
                }

                $couponCartItems = collect($preparedItems)->map(function ($item) {
                    return (object) [
                        'product' => $item['product'],
                        'variant' => $item['variant'],
                        'quantity' => $item['cart_item']->quantity,
                        'line_total' => $item['line_total'],
                    ];
                });

                $couponQuote = $this->calculateCouponDiscount(
                    $coupon,
                    $couponCartItems,
                    $subtotal,
                    $user->id
                );

                $couponDiscount = (float) $couponQuote['discount'];
            }

            /*
             * Allocate coupon discount across eligible items after BOGO.
             * This prevents discounts from exceeding an item's remaining value.
             */
            $couponItemDiscounts = array_fill(
                0,
                count($preparedItems),
                0.0
            );

            if ($coupon && $couponQuote && $couponDiscount > 0) {
                $eligibleIndexes = $couponQuote['eligible_indexes'];

                $remainingEligibleSubtotal = 0;

                foreach ($eligibleIndexes as $index) {
                    $lineTotal = $preparedItems[$index]['line_total'];
                    $bogoItemDiscount = $bogoItemDiscounts[$index] ?? 0;

                    $remainingEligibleSubtotal += max(
                        0,
                        $lineTotal - $bogoItemDiscount
                    );
                }

                $remainingEligibleSubtotal = round(
                    $remainingEligibleSubtotal,
                    2
                );

                $couponDiscount = min(
                    $couponDiscount,
                    $remainingEligibleSubtotal
                );

                $allocatedCouponDiscount = 0;
                $eligibleCount = count($eligibleIndexes);

                foreach ($eligibleIndexes as $position => $index) {
                    $lineTotal = $preparedItems[$index]['line_total'];

                    $bogoItemDiscount = $bogoItemDiscounts[$index] ?? 0;

                    $remainingLine = max(
                        0,
                        round($lineTotal - $bogoItemDiscount, 2)
                    );

                    if ($position === $eligibleCount - 1) {
                        $itemCouponDiscount = round(
                            $couponDiscount - $allocatedCouponDiscount,
                            2
                        );
                    } elseif ($remainingEligibleSubtotal > 0) {
                        $itemCouponDiscount = round(
                            $couponDiscount *
                            ($remainingLine / $remainingEligibleSubtotal),
                            2
                        );

                        $allocatedCouponDiscount += $itemCouponDiscount;
                    } else {
                        $itemCouponDiscount = 0;
                    }

                    $couponItemDiscounts[$index] = min(
                        $remainingLine,
                        max(0, $itemCouponDiscount)
                    );
                }
            }

            /*
             * Recalculate the coupon discount from actual allocated values.
             */
            $couponDiscount = round(
                array_sum($couponItemDiscounts),
                2
            );

            $discount = round(
                $bogoDiscount + $couponDiscount,
                2
            );

            $total = max(
                0,
                round($subtotal + $shipping + $tax - $discount, 2)
            );

            /*
             * Create the order.
             */
            $order = Order::create([
                'order_number' => 'ESS-' .
                    now()->format('Ymd') .
                    '-' .
                    strtoupper(Str::random(8)),

                'user_id' => $user->id,
                'coupon_id' => $coupon?->id,
                'offer_id' => $bogoDiscount > 0 ? $bogoOffer?->id : null,
                'address_id' => $address->id,

                'shipping_name' => $address->name,
                'shipping_phone' => $address->phone,
                'shipping_address_line1' => $address->address_line1,
                'shipping_address_line2' => $address->address_line2,
                'shipping_city' => $address->city,
                'shipping_state' => $address->state,
                'shipping_pincode' => $address->pincode,
                'shipping_country' => $address->country,

                'subtotal' => $subtotal,
                // 'discount_amount' => $discount,
              'discount_amount' => round(
                    (float) $couponDiscount + (float) $bogoDiscount,
                    2
                ),
                'shipping_amount' => $shipping,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'coupon_discount_amount' => $couponDiscount,
                'offer_discount_amount' => $bogoDiscount,

                'status' => $paymentMethod === 'cod'
                    ? 'confirmed'
                    : 'pending',

                'payment_status' => 'pending',
                'customer_note' => $validated['customer_note'] ?? null,
                'placed_at' => now(),

                'payment_expires_at' => $paymentMethod === 'razorpay'
                    ? now()->addMinutes(30)
                    : null,

                'stock_released_at' => null,
            ]);

            /*
             * Create order items and deduct stock.
             */
            foreach ($preparedItems as $index => $item) {
                $product = $item['product'];
                $variant = $item['variant'];
                $cartItem = $item['cart_item'];

                $bogoItemDiscount = (float) (
                    $bogoItemDiscounts[$index] ?? 0
                );

                $couponItemDiscount = (float) (
                    $couponItemDiscounts[$index] ?? 0
                );

                $itemDiscount = round(
                    $bogoItemDiscount + $couponItemDiscount,
                    2
                );

                $itemDiscount = min(
                    $item['line_total'],
                    $itemDiscount
                );

                $itemTotal = max(
                    0,
                    round($item['line_total'] - $itemDiscount, 2)
                );

                $primaryImage = $product->images
                    ->firstWhere('is_primary', true)
                    ?? $product->images->first();

                OrderItem::create([
                    'order_id' => $order->id,
                    'cart_item_id' => $cartItem->id,
                    'vendor_id' => $product->vendor_id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,

                    'product_name' => $product->name,
                    'product_sku' => $variant?->sku ?? $product->sku,
                    'variant_name' => $variant?->variant_name,
                    'product_image' => $primaryImage?->image_path,

                    'quantity' => $cartItem->quantity,
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['line_total'],
                    'discount_amount' => $itemDiscount,
                    'tax_amount' => 0,
                    'total_amount' => $itemTotal,
                    'commission_amount' => 0,
                    'vendor_earning' => $itemTotal,
                ]);

                if ($variant) {
                    $variant->decrement(
                        'stock_quantity',
                        $cartItem->quantity
                    );
                } else {
                    $product->decrement(
                        'stock_quantity',
                        $cartItem->quantity
                    );
                }
            }

            /*
             * Record coupon usage.
             */
            // if ($coupon && $couponDiscount > 0) {
            //     CouponUsage::create([
            //         'coupon_id' => $coupon->id,
            //         'user_id' => $user->id,
            //         'order_id' => $order->id,
            //         'discount_amount' => $couponDiscount,
            //         'used_at' => now(),
            //     ]);

            //     $coupon->increment('used_count');
            // }

            /*
             * Record BOGO usage.
             * One usage is recorded per order, not per free unit.
             */
            // if ($bogoOffer && $bogoDiscount > 0) {
            //     OfferUsage::create([
            //         'offer_id' => $bogoOffer->id,
            //         'user_id' => $user->id,
            //         'order_id' => $order->id,
            //         'discount_amount' => $bogoDiscount,
            //         'used_at' => now(),
            //     ]);

            //     $bogoOffer->increment('used_count');
            // }

            /*
             * Create payment record using the final discounted total.
             */
            Payment::create([
                'order_id' => $order->id,
                'payment_number' => 'PAY-' . strtoupper(Str::random(12)),
                'gateway' => $paymentMethod,
                'amount' => $total,
                'currency' => 'INR',
                'status' => $paymentMethod === 'cod'
                    ? 'pending'
                    : 'created',
            ]);

            /*
             * Empty the cart only after all order records are created.
             */
            // $user->cartItems()->delete();

            return $order;
               });

               // COD orders are confirmed immediately.
        // Razorpay orders remain pending until payment is captured.
        if ($paymentMethod === 'cod') {
            $finalizer = app(OrderFinalizationService::class);

            $finalizer->finalizeOrderUsage($order);
            $finalizer->sendOrderNotifications($order);
            $finalizer->removePurchasedCartItems($order);
        }



        /*
         * Clear the coupon session only after successful order creation.
         */
        $request->session()->forget(self::COUPON_SESSION_KEY);

        if ($paymentMethod === 'razorpay') {
            return redirect()->route(
                'account.orders.razorpay.initiate',
                $order
            );
        }

        return redirect()
            ->route('account.orders.show', $order)
            ->with('success', 'Your COD order has been placed successfully!');
    }

    /**
     * Calculate cart subtotal.
     */
    private function cartSubtotal(Collection $cartItems): float
    {
        $subtotal = 0;

        foreach ($cartItems as $item) {
            $product = $item->product;

            if (!$product) {
                continue;
            }

            $variant = $item->variant;

            $price = $variant
                ? ($variant->sale_price ?? $variant->regular_price)
                : $product->current_price;

            $subtotal += (float) $price * (int) $item->quantity;
        }

        return round($subtotal, 2);
    }

    /**
     * Validate coupon and calculate its eligible discount.
     */
    private function calculateCouponDiscount(
        Coupon $coupon,
        Collection $items,
        float $subtotal,
        int $userId
    ): array {
        if ($coupon->status !== 'active') {
            throw ValidationException::withMessages([
                'coupon_code' => 'This coupon is not active.',
            ]);
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            throw ValidationException::withMessages([
                'coupon_code' => 'This coupon is not active yet.',
            ]);
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'coupon_code' => 'This coupon has expired.',
            ]);
        }

        if (
            $coupon->usage_limit !== null &&
            $coupon->used_count >= $coupon->usage_limit
        ) {
            throw ValidationException::withMessages([
                'coupon_code' => 'This coupon usage limit has been reached.',
            ]);
        }

        if ($coupon->usage_limit_per_user !== null) {
            $userUsageCount = CouponUsage::where('coupon_id', $coupon->id)
                ->where('user_id', $userId)
                ->count();

            if ($userUsageCount >= $coupon->usage_limit_per_user) {
                throw ValidationException::withMessages([
                    'coupon_code' => 'You have already reached this coupon usage limit.',
                ]);
            }
        }

        if ($coupon->first_order_only) {
            $hasPreviousOrder = Order::where('user_id', $userId)
                ->whereNotIn('status', ['cancelled', 'returned'])
                ->exists();

            if ($hasPreviousOrder) {
                throw ValidationException::withMessages([
                    'coupon_code' => 'This coupon is valid only for your first order.',
                ]);
            }
        }

        if ($subtotal < (float) $coupon->minimum_order_amount) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Minimum order amount for this coupon is ₹' .
                    number_format(
                        (float) $coupon->minimum_order_amount,
                        2
                    ) . '.',
            ]);
        }

        $productIds = $coupon->products()
            ->pluck('products.id')
            ->all();

        $categoryIds = $coupon->categories()
            ->pluck('categories.id')
            ->all();

        $vendorIds = $coupon->vendors()
            ->pluck('vendors.id')
            ->all();

        $eligibleIndexes = [];
        $eligibleSubtotal = 0;

        foreach ($items as $index => $item) {
            $product = $item->product;

            if (!$product) {
                continue;
            }

            if (
                $productIds &&
                !in_array($product->id, $productIds)
            ) {
                continue;
            }

            if (
                $categoryIds &&
                !in_array($product->category_id, $categoryIds)
            ) {
                continue;
            }

            if (
                $vendorIds &&
                !in_array($product->vendor_id, $vendorIds)
            ) {
                continue;
            }

            $lineTotal = isset($item->line_total)
                ? (float) $item->line_total
                : 0;

            if ($lineTotal <= 0 && isset($item->quantity)) {
                $variant = $item->variant ?? null;

                $price = $variant
                    ? ($variant->sale_price ?? $variant->regular_price)
                    : $product->current_price;

                $lineTotal = (float) $price * (int) $item->quantity;
            }

            if ($lineTotal <= 0) {
                continue;
            }

            $eligibleIndexes[] = $index;
            $eligibleSubtotal += $lineTotal;
        }

        $eligibleSubtotal = round($eligibleSubtotal, 2);

        if ($eligibleSubtotal <= 0) {
            throw ValidationException::withMessages([
                'coupon_code' => 'This coupon does not apply to the products in your cart.',
            ]);
        }

        if ($coupon->discount_type === 'percentage') {
            $discount = $eligibleSubtotal *
                ((float) $coupon->discount_value / 100);
        } elseif ($coupon->discount_type === 'fixed') {
            $discount = min(
                (float) $coupon->discount_value,
                $eligibleSubtotal
            );
        } else {
            // Shipping is currently zero in this checkout implementation.
            $discount = 0;
        }

        if ($coupon->maximum_discount !== null) {
            $discount = min(
                $discount,
                (float) $coupon->maximum_discount
            );
        }

        $discount = round(
            min($discount, $eligibleSubtotal),
            2
        );

        return [
            'discount' => $discount,
            'eligible_subtotal' => $eligibleSubtotal,
            'eligible_indexes' => $eligibleIndexes,
        ];
    }

    /**
     * Create a Razorpay order.
     */
    private function createRazorpayOrder($amount, $receipt)
    {
        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');

        if (!$key || !$secret) {
            throw new \RuntimeException(
                'Razorpay API credentials are not configured.'
            );
        }

        $api = new Api($key, $secret);

        return $api->order->create([
            'receipt' => $receipt,
            'amount' => (int) round($amount * 100),
            'currency' => 'INR',
        ]);
    }


/**
 * Record coupon and BOGO usage only after an order is finalised.
 * Safe to call more than once for the same order.
 */
private function finalizeOrderUsage(Order $order): void
{
    DB::transaction(function () use ($order) {
        $lockedOrder = Order::whereKey($order->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($lockedOrder->coupon_id) {
            $coupon = Coupon::whereKey($lockedOrder->coupon_id)
                ->lockForUpdate()
                ->first();

            if (
                $coupon &&
                !CouponUsage::where('order_id', $lockedOrder->id)->exists()
            ) {
                CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'user_id' => $lockedOrder->user_id,
                    'order_id' => $lockedOrder->id,
                    'discount_amount' => $this->couponDiscountForOrder($lockedOrder),
                    'used_at' => now(),
                ]);

                $coupon->increment('used_count');
            }
        }

        if ($lockedOrder->offer_id) {
            $offer = Offer::whereKey($lockedOrder->offer_id)
                ->lockForUpdate()
                ->first();

            if (
                $offer &&
                !OfferUsage::where('order_id', $lockedOrder->id)->exists()
            ) {
                $bogoDiscount = (float) $lockedOrder->items()
                    ->sum('discount_amount');

                // Record BOGO usage only if the order actually received a BOGO discount.
                if ($bogoDiscount > 0) {
                    OfferUsage::create([
                        'offer_id' => $offer->id,
                        'user_id' => $lockedOrder->user_id,
                        'order_id' => $lockedOrder->id,
                        'discount_amount' => $bogoDiscount,
                        'used_at' => now(),
                    ]);

                    $offer->increment('used_count');
                }
            }
        }
    });
}

/**
 * Get the coupon discount allocated to this order's items.
 */
// private function couponDiscountForOrder(Order $order): float
// {
//     $bogoDiscount = 0;

//     if ($order->offer_id) {
//         $bogoDiscount = (float) OfferUsage::where('order_id', $order->id)
//             ->value('discount_amount');
//     }

//     return max(
//         0,
//         round((float) $order->discount_amount - $bogoDiscount, 2)
//     );
// }

/**
 * Send order-created notifications to customer, vendors and admins.
 */
private function sendOrderNotifications(Order $order): void
{
    try {
        $order->loadMissing(['user', 'items.vendor.user']);

        if ($order->user) {
            $order->user->notify(
                new NewOrderNotification($order->order_number, 'customer')
            );
        }

        $vendors = $order->items
            ->pluck('vendor')
            ->filter()
            ->unique('id');

        foreach ($vendors as $vendor) {
            if ($vendor->user) {
                $vendor->user->notify(
                    new NewOrderNotification($order->order_number, 'vendor')
                );
            }
        }

        $admins = User::where('status', 'active')
            ->whereHas('roles', function ($query) {
                $query->where('slug', 'admin');
            })
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(
                new NewOrderNotification($order->order_number, 'admin')
            );
        }
    } catch (\Throwable $e) {
        report($e);
    }
}




}