<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offer extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'offer_type',
        'discount_value',
        'maximum_discount',
        'minimum_order_amount',
        'buy_quantity',
        'get_quantity',
        'usage_limit',
        'used_count',
        'is_stackable',
        'priority',
        'starts_at',
        'expires_at',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'maximum_discount' => 'decimal:2',
            'minimum_order_amount' => 'decimal:2',
            'buy_quantity' => 'integer',
            'get_quantity' => 'integer',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'is_stackable' => 'boolean',
            'priority' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'offer_products')
            ->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'offer_categories')
            ->withTimestamps();
    }

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'offer_vendors')
            ->withTimestamps();
    }

    public function usages(): HasMany
    {
        return $this->hasMany(OfferUsage::class);
    }
}
