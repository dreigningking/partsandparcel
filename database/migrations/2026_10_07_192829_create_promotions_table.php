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
        Schema::create('promotion_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->decimal('views', 10, 0)->default(0.00); //cost of views
            $table->decimal('clicks', 10, 0)->default(0.00); //cost of clicks
            $table->timestamps();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('listing_id')->constrained('listings')->nullOnDelete();
            $table->enum('type',['views','clicks'])->default('clicks');
            $table->unsignedInteger('achieved_count')->default(0);
            $table->enum('status', ['pending', 'active', 'inactive', 'completed'])->default('pending');
            $table->timestamps();
            $table->index('status');
            $table->index(['user_id', 'listing_id']);
        });

        Schema::create('promo_codes', function (Blueprint $table) {
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('promotion_plans');
    }
};
