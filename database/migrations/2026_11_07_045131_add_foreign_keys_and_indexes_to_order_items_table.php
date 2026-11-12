<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->cascadeOnDelete();

            $table->foreign('vendor_id')
                ->references('id')
                ->on('vendors')
                ->restrictOnDelete();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->nullOnDelete();

            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->nullOnDelete();

            $table->index(
                ['vendor_id', 'created_at'],
                'order_items_vendor_created_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {

            $table->dropForeign(['order_id']);
            $table->dropForeign(['vendor_id']);
            $table->dropForeign(['product_id']);
            $table->dropForeign(['product_variant_id']);

            $table->dropIndex('order_items_vendor_created_index');
        });
    }
};
