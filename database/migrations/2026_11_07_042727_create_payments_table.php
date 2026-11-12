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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();

        $table->foreignId('order_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->string('payment_number', 60)->unique();

        $table->string('gateway', 50)->default('razorpay');

        $table->string('gateway_order_id', 150)->nullable()->index();
        $table->string('gateway_payment_id', 150)->nullable()->index();

        $table->string('gateway_signature', 500)->nullable();

        $table->decimal('amount', 12, 2);

        $table->string('currency', 3)->default('INR');

        $table->enum('status', [
            'created',
            'pending',
            'authorized',
            'captured',
            'failed',
            'refunded'
        ])->default('created');

        $table->text('failure_reason')->nullable();

        $table->timestamp('paid_at')->nullable();

        $table->timestamps();

        $table->index(['order_id', 'status']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
