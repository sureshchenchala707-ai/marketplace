<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();

            $table->enum('discount_type', [
                'percentage',
                'fixed',
                'free_shipping',
            ]);

            $table->decimal('discount_value', 12, 2)->default(0);
            $table->decimal('maximum_discount', 12, 2)->nullable();
            $table->decimal('minimum_order_amount', 12, 2)->default(0);

            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_limit_per_user')->nullable();
            $table->unsignedInteger('used_count')->default(0);

            $table->boolean('first_order_only')->default(false);
            $table->boolean('is_stackable')->default(false);

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->enum('status', ['active', 'inactive', 'expired'])
                ->default('inactive');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'starts_at', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
