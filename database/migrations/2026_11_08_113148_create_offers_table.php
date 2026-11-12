
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->enum('offer_type', [
                'percentage',
                'fixed',
                'free_shipping',
                'bogo',
            ]);

            $table->decimal('discount_value', 12, 2)->default(0);
            $table->decimal('maximum_discount', 12, 2)->nullable();
            $table->decimal('minimum_order_amount', 12, 2)->default(0);

            $table->unsignedInteger('buy_quantity')->nullable();
            $table->unsignedInteger('get_quantity')->nullable();

            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);

            $table->boolean('is_stackable')->default(false);
            $table->unsignedInteger('priority')->default(0);

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->enum('status', [
                'active',
                'inactive',
                'expired',
            ])->default('inactive');

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
        Schema::dropIfExists('offers');
    }
};
