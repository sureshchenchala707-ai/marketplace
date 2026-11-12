<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();

        $table->string('order_number', 50)->unique();

        $table->foreignId('user_id')
            ->constrained()
            ->restrictOnDelete();

        $table->foreignId('address_id')
            ->nullable()
            ->constrained('customer_addresses')
            ->nullOnDelete();

        // Shipping address snapshot
        $table->string('shipping_name', 150);
        $table->string('shipping_phone', 20);
        $table->string('shipping_address_line1', 255);
        $table->string('shipping_address_line2', 255)->nullable();
        $table->string('shipping_city', 100);
        $table->string('shipping_state', 100);
        $table->string('shipping_pincode', 10);
        $table->string('shipping_country', 100)->default('India');

        $table->decimal('subtotal', 12, 2)->default(0);
        $table->decimal('discount_amount', 12, 2)->default(0);
        $table->decimal('shipping_amount', 12, 2)->default(0);
        $table->decimal('tax_amount', 12, 2)->default(0);
        $table->decimal('total_amount', 12, 2)->default(0);

        $table->enum('status', [
            'pending',
            'confirmed',
            'processing',
            'shipped',
            'delivered',
            'cancelled',
            'returned'
        ])->default('pending');

        $table->enum('payment_status', [
            'pending',
            'paid',
            'failed',
            'refunded',
            'partially_refunded'
        ])->default('pending');

        $table->text('customer_note')->nullable();

        $table->timestamp('placed_at')->nullable();
        $table->timestamp('delivered_at')->nullable();
        $table->timestamp('cancelled_at')->nullable();

        $table->timestamps();

        $table->index(['user_id', 'status']);
        $table->index(['payment_status', 'created_at']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
