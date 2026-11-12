<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('vendor_status', [
                'pending',
                'processing',
                'shipped',
                'delivered',
                'cancelled',
            ])->default('pending')->after('vendor_earning');

            $table->string('shipping_carrier', 100)->nullable();
            $table->string('tracking_number', 150)->nullable();

            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->index(
                ['vendor_id', 'vendor_status'],
                'order_items_vendor_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_vendor_status_index');

            $table->dropColumn([
                'vendor_status',
                'shipping_carrier',
                'tracking_number',
                'shipped_at',
                'delivered_at',
            ]);
        });
    }
};
