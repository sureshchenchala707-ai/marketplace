<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Offer;
use App\Models\OfferUsage;
use App\Models\User;
use App\Models\CartItem;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\DB;

class OrderFinalizationService
{
    /**
     * Record coupon and offer usage exactly once per order.
     */
    public function finalizeOrderUsage(Order $order): void
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
                        'discount_amount' => $lockedOrder->coupon_discount_amount,
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

                    // Record BOGO usage only when a discount was actually applied.
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
     * Notify the customer, vendors, and admins about a finalized order.
     */
    public function sendOrderNotifications(Order $order): void
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

    /**
 * Remove only the quantities purchased from the original cart rows.
 */
public function removePurchasedCartItems(Order $order): void
{
    DB::transaction(function () use ($order) {
        $items = $order->items()->get();

        foreach ($items as $item) {
            if (!$item->cart_item_id) {
                continue;
            }

            $cartItem = CartItem::whereKey($item->cart_item_id)
                ->where('user_id', $order->user_id)
                ->lockForUpdate()
                ->first();

            if (!$cartItem) {
                continue;
            }

            $remainingQuantity = (int) $cartItem->quantity
                - (int) $item->quantity;

            if ($remainingQuantity <= 0) {
                $cartItem->delete();
            } else {
                $cartItem->update([
                    'quantity' => $remainingQuantity,
                ]);
            }
        }
    });
}
}
