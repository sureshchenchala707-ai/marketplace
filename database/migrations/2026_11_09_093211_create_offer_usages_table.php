<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('offer_id')
                ->constrained('offers')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->restrictOnDelete();

            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->timestamp('used_at')->useCurrent();
            $table->timestamps();

            $table->unique(
                ['offer_id', 'order_id'],
                'offer_usages_offer_order_unique'
            );

            $table->index(
                ['offer_id', 'user_id'],
                'offer_usages_offer_user_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_usages');
    }
};
