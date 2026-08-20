<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 15, 2);
            $table->string('billing_interval')->default('monthly');
            $table->unsignedInteger('response_limit');
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained()->restrictOnDelete();
            $table->string('status')->default('active');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->unsignedInteger('response_limit');
            $table->timestamps();
            $table->index(['user_id', 'status', 'starts_at', 'ends_at']);
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issuer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('offer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('discussion_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('delivery_fee', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('status')->default('draft');
            $table->unsignedInteger('warranty_period_days')->nullable();
            $table->text('warranty_terms')->nullable();
            $table->timestamp('warranty_starts_at')->nullable();
            $table->timestamp('warranty_ends_at')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamps();
            $table->index(['issuer_id', 'status']);
            $table->index(['customer_id', 'status']);
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('line_total', 15, 2);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->unique();
            $table->string('provider')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('NGN');
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });

        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // commission|subscription|service_fee|other
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('NGN');
            $table->timestamps();
            $table->index(['type', 'created_at']);
        });

        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->decimal('gross_amount', 15, 2)->default(0);
            $table->decimal('commission', 15, 2)->default(0);
            $table->decimal('refunds', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamp('eligible_at')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();
            $table->index(['seller_id', 'status']);
        });

        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('NGN');
            $table->string('status')->default('pending');
            $table->string('provider')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('payout_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('settlement_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 15, 2);
            $table->timestamps();
            $table->unique(['payout_id', 'settlement_id']);
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('pending');
            $table->string('reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('refund_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('refund_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('invoice_item_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_items');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payout_settlements');
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('settlements');
        Schema::dropIfExists('revenues');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
