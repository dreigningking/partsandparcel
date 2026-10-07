<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete();
            $table->string('type');
            $table->string('status')->default('open');
            $table->text('description');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['invoice_id', 'status']);
        });

        Schema::create('issue_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_item_id')->nullable()->constrained()->nullOnDelete();
            $table->text('reason')->nullable();
            $table->text('evidence')->nullable();
            $table->timestamps();
        });

        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'disputed', 'resolved'])->default('pending');
            $table->enum('claim_type', ['defect', 'hardware_failure', 'malfunction', 'wear_tear'])->default('defect');
            $table->text('description');
            $table->text('evidence')->nullable();
            $table->text('seller_notes')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('disputed_at')->nullable();
            $table->timestamps();
            $table->index(['invoice_id', 'status']);
        });

        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('issue_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warranty_claim_id')->nullable()->constrained('warranty_claims')->nullOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->string('delivery_method')->nullable(); // pickup|dropoff|shipment
            $table->foreignId('shipment_id')->nullable()->constrained('shipments')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('rejection_evidence')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('disputed_at')->nullable();
            $table->timestamps();
            
        });

        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_item_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('condition_status')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('replacements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('issue_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warranty_claim_id')->nullable()->constrained('warranty_claims')->nullOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->string('delivery_method')->nullable();
            $table->foreignId('shipment_id')->nullable()->constrained('shipments')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('rejection_evidence')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('disputed_at')->nullable();
            $table->timestamps();
            
        });

        Schema::create('replacement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('replacement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('listing_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
        });

        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('issue_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('return_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('replacement_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warranty_claim_id')->nullable()->constrained('warranty_claims')->nullOnDelete();
            $table->foreignId('refund_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('opened_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('respondent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['rejection_contested','replacement_defective','return_fraud_abuse','warranty_denial','mutual_deadlock',])->default('rejection_contested');
            $table->string('status')->default('open');
            $table->text('reason');
            $table->text('evidence')->nullable();
            $table->text('resolution')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('dispute_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispute_id')->constrained()->cascadeOnDelete();
            $table->morphs('itemable');
            $table->text('claim')->nullable();
            $table->text('evidence')->nullable();
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('dispute_items');
        Schema::dropIfExists('disputes');
        Schema::dropIfExists('warranty_claims');
        Schema::dropIfExists('replacement_items');
        Schema::dropIfExists('replacements');
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('returns');
        Schema::dropIfExists('issue_items');
        Schema::dropIfExists('issues');
    }
};
