<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\BogoOfferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    private function availableProduct(Product $product): bool
    {
        return $product->status === 'approved'
            && $product->is_active
            && $product->vendor?->status === 'approved'
            && $product->category?->is_active;
    }

    /**
     * Display cart with applicable BOGO savings.
     */
    public function index()
    {
        $cartItems = auth()->user()->cartItems()
            ->with([
                'product.vendor',
                'product.category',
                'product.images',
                'variant',
            ])
            ->latest()
            ->get();

        $subtotal = 0;

        foreach ($cartItems as $item) {
            if (!$item->product) {
                $item->unit_price = 0;
                $item->line_total = 0;
                continue;
            }

            $price = $item->variant
                ? ($item->variant->sale_price ?? $item->variant->regular_price)
                : $item->product->current_price;

            $item->unit_price = (float) $price;
            $item->line_total = round(
                (float) $price * (int) $item->quantity,
                2
            );

            $subtotal += $item->line_total;
        }

        $subtotal = round($subtotal, 2);

        /*
         * Find the active marketplace-wide BOGO offer.
         */
        $bogoService = app(BogoOfferService::class);
        $bogoOffer = $bogoService->activeOffer();

        if (
            $bogoOffer &&
            $subtotal < (float) $bogoOffer->minimum_order_amount
        ) {
            $bogoOffer = null;
        }

        /*
         * Calculate free units grouped by product ID.
         * Different variants of the same product qualify together.
         */
        $bogoQuote = $bogoService->calculate(
            $cartItems->filter(function ($item) {
                return $item->product !== null;
            })->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity' => (int) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                ];
            }),
            $bogoOffer
        );

        $bogoOffer = $bogoQuote['offer'];
        $bogoDiscount = (float) $bogoQuote['discount'];
        $bogoItemDiscounts = $bogoQuote['item_discounts'];

        /*
         * Attach discount values to each cart row.
         */
        foreach ($cartItems as $index => $item) {
            $itemDiscount = (float) (
                $bogoItemDiscounts[$index] ?? 0
            );

            $item->bogo_discount = round($itemDiscount, 2);

            $item->discounted_line_total = max(
                0,
                round($item->line_total - $item->bogo_discount, 2)
            );
        }

        $payableSubtotal = max(
            0,
            round($subtotal - $bogoDiscount, 2)
        );

        return view('customer.cart.index', compact(
            'cartItems',
            'subtotal',
            'bogoOffer',
            'bogoDiscount',
            'payableSubtotal'
        ));
    }

    /**
     * Add a product to the cart.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::with('vendor', 'category')
                ->whereKey($validated['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (!$this->availableProduct($product)) {
                abort(422, 'This product is currently unavailable.');
            }

            $variant = null;

            if (!empty($validated['variant_id'])) {
                $variant = ProductVariant::whereKey($validated['variant_id'])
                    ->where('product_id', $product->id)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->first();

                if (!$variant) {
                    abort(422, 'Selected variant is unavailable.');
                }
            } elseif ($product->variants()->where('is_active', true)->exists()) {
                abort(422, 'Please select a product variant.');
            }

            $availableStock = $variant
                ? (int) $variant->stock_quantity
                : (int) $product->stock_quantity;

            $userId = auth()->id();

            $cartItem = CartItem::where('user_id', $userId)
                ->where('product_id', $product->id)
                ->when(
                    $variant,
                    fn ($query) => $query->where(
                        'product_variant_id',
                        $variant->id
                    ),
                    fn ($query) => $query->whereNull('product_variant_id')
                )
                ->lockForUpdate()
                ->first();

            $newQuantity = ($cartItem?->quantity ?? 0)
                + $validated['quantity'];

            if ($availableStock < $newQuantity) {
                abort(
                    422,
                    "Only {$availableStock} units are currently available."
                );
            }

            if ($cartItem) {
                $cartItem->update([
                    'quantity' => $newQuantity,
                ]);
            } else {
                CartItem::create([
                    'user_id' => $userId,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'quantity' => $newQuantity,
                ]);
            }
        });

        return redirect()
            ->route('account.cart.index')
            ->with('success', 'Product added to your cart successfully.');
    }

    /**
     * Update cart quantity.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        abort_unless(
            $cartItem->user_id === auth()->id(),
            404
        );

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        DB::transaction(function () use ($cartItem, $validated) {
            $item = CartItem::whereKey($cartItem->id)
                ->lockForUpdate()
                ->firstOrFail();

            $product = Product::with('vendor', 'category')
                ->whereKey($item->product_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!$this->availableProduct($product)) {
                abort(422, 'This product is currently unavailable.');
            }

            $variant = null;

            if ($item->product_variant_id) {
                $variant = ProductVariant::whereKey($item->product_variant_id)
                    ->where('product_id', $product->id)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->first();

                if (!$variant) {
                    abort(422, 'This product variant is no longer available.');
                }
            } elseif ($product->variants()->where('is_active', true)->exists()) {
                abort(422, 'Please select a product variant.');
            }

            $stock = $variant
                ? (int) $variant->stock_quantity
                : (int) $product->stock_quantity;

            if ($validated['quantity'] > $stock) {
                abort(422, "Only {$stock} units are available.");
            }

            $item->update([
                'quantity' => $validated['quantity'],
            ]);
        });

        return back()->with('success', 'Cart quantity updated.');
    }

    /**
     * Remove an item from the cart.
     */
    public function destroy(CartItem $cartItem)
    {
        abort_unless(
            $cartItem->user_id === auth()->id(),
            404
        );

        $cartItem->delete();

        return back()->with('success', 'Product removed from cart.');
    }

    /**
     * Clear the customer's cart.
     */
    public function clear()
    {
        auth()->user()->cartItems()->delete();

        return redirect()
            ->route('account.cart.index')
            ->with('success', 'Your cart has been cleared.');
    }
}
