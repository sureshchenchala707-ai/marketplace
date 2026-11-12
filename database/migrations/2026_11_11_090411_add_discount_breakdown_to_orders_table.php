<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('coupon_discount_amount', 12, 2)
                ->default(0)
                ->after('discount_amount');

            $table->decimal('offer_discount_amount', 12, 2)
                ->default(0)
                ->after('coupon_discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'coupon_discount_amount',
                'offer_discount_amount',
            ]);
        });
    }
};