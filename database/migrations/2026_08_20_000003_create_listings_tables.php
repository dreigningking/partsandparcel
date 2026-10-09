<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('slug')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('reserved_quantity')->default(0);
            $table->unsignedInteger('sold_quantity')->default(0);
            $table->decimal('price', 15, 2);
            $table->boolean('is_negotiable')->default(false);
            $table->unsignedInteger('warranty_period_days')->nullable();
            $table->boolean('is_warranty_negotiable')->default(false);
            $table->text('warranty_terms')->nullable();
            $table->boolean('allow_shipping')->default(false);
            $table->boolean('is_published')->default(true);//whether is published by owner or not
            $table->boolean('is_active')->default(false);//whether subscription covers it or not
            $table->timestamps();
            $table->index(['user_id', 'is_published']);
            $table->index(['is_published']);
            $table->index('item_id');
            $table->index('slug');
        });

        Schema::create('listing_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->index(['listing_id', 'rating']);
            $table->index(['listing_id', 'created_at']);
        });

        

        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('active'); // active, ordered, abandoned
            $table->timestamps();
            $table->index(['buyer_id', 'seller_id', 'status']);
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('listing_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->timestamps();
            $table->unique(['cart_id', 'listing_id']);
        });

        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'listing_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('listing_reviews');
        Schema::dropIfExists('listings');
    }
};
