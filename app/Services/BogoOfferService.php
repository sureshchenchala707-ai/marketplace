<?php

namespace App\Services;

use App\Models\Offer;
use Illuminate\Support\Collection;

class BogoOfferService
{
    /**
     * Find the highest-priority active global BOGO offer.
     */
    public function activeOffer(): ?Offer
    {
        return Offer::query()
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
            ->first();
    }

    /**
     * Calculate BOGO discount for cart/order rows.
     *
     * Each row must contain:
     * product_id, quantity, unit_price.
     *
     * Returns discount allocated to each row.
     */
    public function calculate(Collection $items, ?Offer $offer = null): array
    {
        $offer = $offer ?? $this->activeOffer();

        $discounts = [];
        $discountTotal = 0;

        foreach ($items as $index => $item) {
            $discounts[$index] = 0.0;
        }

        if (!$offer) {
            return [
                'offer' => null,
                'discount' => 0,
                'item_discounts' => $discounts,
            ];
        }

        $buyQuantity = max(1, (int) $offer->buy_quantity);
        $getQuantity = max(1, (int) $offer->get_quantity);
        $groupSize = $buyQuantity + $getQuantity;

        if ($groupSize <= 0) {
            return [
                'offer' => null,
                'discount' => 0,
                'item_discounts' => $discounts,
            ];
        }

        // Combine all variants belonging to the same product.
        $groups = [];

        foreach ($items as $index => $item) {
            $productId = (int) data_get($item, 'product_id');
            $quantity = (int) data_get($item, 'quantity');
            $price = round((float) data_get($item, 'unit_price'), 2);

            if ($productId <= 0 || $quantity <= 0 || $price <= 0) {
                continue;
            }

            for ($unit = 0; $unit < $quantity; $unit++) {
                $groups[$productId][] = [
                    'index' => $index,
                    'price' => $price,
                ];
            }
        }

        foreach ($groups as $units) {
            $totalQuantity = count($units);

            $freeQuantity = intdiv($totalQuantity, $groupSize)
                * $getQuantity;

            if ($freeQuantity <= 0) {
                continue;
            }

            // Cheapest units of the same product become free.
            usort($units, function ($a, $b) {
                return $a['price'] <=> $b['price'];
            });

            for ($i = 0; $i < $freeQuantity; $i++) {
                $unit = $units[$i];

                $discounts[$unit['index']] += $unit['price'];
                $discountTotal += $unit['price'];
            }
        }

        foreach ($discounts as $index => $amount) {
            $discounts[$index] = round($amount, 2);
        }

        return [
            'offer' => $discountTotal > 0 ? $offer : null,
            'discount' => round($discountTotal, 2),
            'item_discounts' => $discounts,
        ];
    }
}