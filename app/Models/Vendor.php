<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_name',
        'slug',
        'business_email',
        'business_phone',
        'gst_number',
        'pan_number',
        'business_address',
        'city',
        'state',
        'pincode',
        'status',
        'admin_remarks',
        'approved_by',
        'approved_at',
        'logo',
        'banner',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}