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
            $table->string('status')->default('open'); // open, resolved, closed, escalated
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
            $table->string('status')->default('pending'); // pending, approved, shipped, received, accepted, rejected, disputed, expired, cancelled
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
            $table->string('condition_status')->nullable(); // working, damaged, opened, tampered, unaltered
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
            $table->string('status')->default('pending'); // pending, approved, sent, received, accepted, rejected, disputed, cancelled
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
            $table->foreignId('return_shipment_id')->nullable()->constrained('shipments')->nullOnDelete();
            $table->foreignId('opened_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('respondent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['rejection_contested','replacement_defective','return_fraud_abuse','warranty_denial','mutual_deadlock',])->default('rejection_contested');
            $table->enum('decision', ['buyer_favor', 'seller_favor', 'split'])->nullable();
            $table->decimal('refund_amount', 15, 2)->nullable();
            $table->boolean('require_return')->default(false);
            $table->text('resolution_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('status')->default('open'); // open, under_review, evidence_required, resolved, closed
            $table->text('reason');
            $table->text('respondent_defense')->nullable();
            $table->timestamp('respondent_defended_at')->nullable();
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

        Schema::create('dispute_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispute_id')->constrained('disputes')->cascadeOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('target_party', ['buyer', 'seller'])->default('seller');
            $table->foreignId('target_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->string('deadline_preset')->default('24_hours');
            $table->timestamp('deadline_at')->nullable();
            $table->enum('status', ['pending', 'submitted', 'overdue', 'cancelled'])->default('pending');
            $table->text('party_notes')->nullable();
            $table->json('files')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['dispute_id', 'status']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('dispute_evidence');
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
