<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Create dispute_evidence table
        if (! Schema::hasTable('dispute_evidence')) {
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

        // 2. Add arbitration decision, defense, and logistics columns to disputes table
        Schema::table('disputes', function (Blueprint $table) {
            if (! Schema::hasColumn('disputes', 'decision')) {
                $table->enum('decision', ['buyer_favor', 'seller_favor', 'split'])->nullable()->after('type');
            }
            if (! Schema::hasColumn('disputes', 'refund_amount')) {
                $table->decimal('refund_amount', 15, 2)->nullable()->after('decision');
            }
            if (! Schema::hasColumn('disputes', 'require_return')) {
                $table->boolean('require_return')->default(false)->after('refund_amount');
            }
            if (! Schema::hasColumn('disputes', 'resolution_notes')) {
                $table->text('resolution_notes')->nullable()->after('require_return');
            }
            if (! Schema::hasColumn('disputes', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->after('resolution_notes');
            }
            if (! Schema::hasColumn('disputes', 'respondent_defense')) {
                $table->text('respondent_defense')->nullable()->after('reason');
            }
            if (! Schema::hasColumn('disputes', 'respondent_defended_at')) {
                $table->timestamp('respondent_defended_at')->nullable()->after('respondent_defense');
            }
            if (! Schema::hasColumn('disputes', 'return_shipment_id')) {
                $table->foreignId('return_shipment_id')->nullable()->after('refund_id')->constrained('shipments')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('disputes', function (Blueprint $table) {
            if (Schema::hasColumn('disputes', 'return_shipment_id')) {
                $table->dropForeign(['return_shipment_id']);
                $table->dropColumn('return_shipment_id');
            }
            if (Schema::hasColumn('disputes', 'respondent_defended_at')) {
                $table->dropColumn('respondent_defended_at');
            }
            if (Schema::hasColumn('disputes', 'respondent_defense')) {
                $table->dropColumn('respondent_defense');
            }
            if (Schema::hasColumn('disputes', 'internal_notes')) {
                $table->dropColumn('internal_notes');
            }
            if (Schema::hasColumn('disputes', 'resolution_notes')) {
                $table->dropColumn('resolution_notes');
            }
            if (Schema::hasColumn('disputes', 'require_return')) {
                $table->dropColumn('require_return');
            }
            if (Schema::hasColumn('disputes', 'refund_amount')) {
                $table->dropColumn('refund_amount');
            }
            if (Schema::hasColumn('disputes', 'decision')) {
                $table->dropColumn('decision');
            }
        });

        Schema::dropIfExists('dispute_evidence');
    }
};
