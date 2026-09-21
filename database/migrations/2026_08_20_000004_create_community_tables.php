<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type',['item','service','delivery','advice'])->default('item');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('model_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('body');
            $table->text('attachments')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();
            $table->index(['category_id', 'status']);
            $table->index(['model_id', 'status']);
        });

        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body')->nullable();
            $table->string('status')->default('visible');
            $table->timestamps();
            $table->index(['discussion_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });


        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('offers')->nullOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('discussion_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('response_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cart_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('delivery_method', ['buyer_responsible','seller_responsible','platform_responsible'])->nullable();
            $table->decimal('discount', 15, 2)->default(0);
            $table->text('terms')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['discussion_id', 'status']);
            $table->index(['sender_id', 'recipient_id', 'status']);
        });

        Schema::create('offer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('listing_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->enum('type', ['item','service','delivery'])->default('item');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->unsignedInteger('warranty_period_days')->nullable();
            $table->text('warranty_terms')->nullable();
            $table->timestamp('warranty_starts_at')->nullable();
            $table->timestamp('warranty_ends_at')->nullable();
            $table->timestamps();
            $table->index(['offer_id', 'listing_id']);
        });

        
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->morphs('contextable'); // listing, offer, etc.
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->unique(['conversation_id', 'user_id']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users');
            $table->text('body');
            $table->text('attachments')->nullable();
            $table->timestamps();
            $table->index(['conversation_id', 'created_at']);
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversation_participants');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('offer_items');
        Schema::dropIfExists('offers');
        Schema::dropIfExists('responses');
        Schema::dropIfExists('discussions');
    }
};
