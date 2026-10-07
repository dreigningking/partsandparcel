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
        if (! Schema::hasTable('promotions')) {
            Schema::create('promotions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('listing_id')->nullable()->constrained('listings')->nullOnDelete();
                $table->enum('type', ['views', 'clicks'])->default('clicks');
                $table->unsignedInteger('target_count')->default(0);
                $table->unsignedInteger('achieved_count')->default(0);
                $table->enum('status', ['pending', 'active', 'inactive', 'completed'])->default('pending');
                $table->timestamps();
                $table->index('status');
                $table->index(['user_id', 'listing_id']);
            });
        }

        if (! Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('type')->default('percentage'); // percentage | fixed
                $table->decimal('value', 15, 2);
                $table->decimal('min_order_amount', 15, 2)->default(0.00);
                $table->decimal('max_discount', 15, 2)->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->unsignedInteger('usage_limit')->nullable();
                $table->unsignedInteger('used_count')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('promotions');
    }
};
