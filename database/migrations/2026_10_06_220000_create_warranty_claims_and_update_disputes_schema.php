<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Create warranty_claims table
        if (! Schema::hasTable('warranty_claims')) {
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
        }

        // 2. Update returns table with warranty_claim_id and rejection details
        Schema::table('returns', function (Blueprint $table) {
            if (! Schema::hasColumn('returns', 'warranty_claim_id')) {
                $table->foreignId('warranty_claim_id')->nullable()->after('issue_id')->constrained('warranty_claims')->nullOnDelete();
            }
            if (! Schema::hasColumn('returns', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('returns', 'rejection_evidence')) {
                $table->text('rejection_evidence')->nullable()->after('rejection_reason');
            }
        });

        // 3. Update replacements table with warranty_claim_id and rejection details
        Schema::table('replacements', function (Blueprint $table) {
            if (! Schema::hasColumn('replacements', 'warranty_claim_id')) {
                $table->foreignId('warranty_claim_id')->nullable()->after('issue_id')->constrained('warranty_claims')->nullOnDelete();
            }
            if (! Schema::hasColumn('replacements', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('replacements', 'rejection_evidence')) {
                $table->text('rejection_evidence')->nullable()->after('rejection_reason');
            }
            if (! Schema::hasColumn('replacements', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('accepted_at');
            }
            if (! Schema::hasColumn('replacements', 'disputed_at')) {
                $table->timestamp('disputed_at')->nullable()->after('rejected_at');
            }
        });

        // 4. Update disputes table
        // Drop shipment_id if present
        if (Schema::hasColumn('disputes', 'shipment_id')) {
            Schema::table('disputes', function (Blueprint $table) {
                // Drop foreign key first if it exists
                try {
                    $table->dropForeign(['shipment_id']);
                } catch (\Throwable $e) {
                    // Ignore if foreign key was already dropped or named differently
                }
                $table->dropColumn('shipment_id');
            });
        }

        Schema::table('disputes', function (Blueprint $table) {
            if (! Schema::hasColumn('disputes', 'invoice_id')) {
                $table->foreignId('invoice_id')->nullable()->after('id')->constrained('invoices')->cascadeOnDelete();
            }
            if (! Schema::hasColumn('disputes', 'warranty_claim_id')) {
                $table->foreignId('warranty_claim_id')->nullable()->after('replacement_id')->constrained('warranty_claims')->nullOnDelete();
            }
            if (! Schema::hasColumn('disputes', 'respondent_id')) {
                $table->foreignId('respondent_id')->nullable()->after('opened_by')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('disputes', 'type')) {
                $table->enum('type', [
                    'rejection_contested',
                    'replacement_defective',
                    'return_fraud_abuse',
                    'warranty_denial',
                    'mutual_deadlock',
                ])->default('rejection_contested')->after('respondent_id');
            }
            if (! Schema::hasColumn('disputes', 'evidence')) {
                $table->text('evidence')->nullable()->after('reason');
            }
        });

        // 5. Backfill invoice_id on any existing disputes through issue_id
        $disputesWithoutInvoice = DB::table('disputes')
            ->whereNull('invoice_id')
            ->whereNotNull('issue_id')
            ->get();

        foreach ($disputesWithoutInvoice as $d) {
            $invoiceId = DB::table('issues')->where('id', $d->issue_id)->value('invoice_id');
            if ($invoiceId) {
                DB::table('disputes')->where('id', $d->id)->update(['invoice_id' => $invoiceId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('disputes', function (Blueprint $table) {
            if (Schema::hasColumn('disputes', 'evidence')) {
                $table->dropColumn('evidence');
            }
            if (Schema::hasColumn('disputes', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('disputes', 'respondent_id')) {
                $table->dropForeign(['respondent_id']);
                $table->dropColumn('respondent_id');
            }
            if (Schema::hasColumn('disputes', 'warranty_claim_id')) {
                $table->dropForeign(['warranty_claim_id']);
                $table->dropColumn('warranty_claim_id');
            }
            if (Schema::hasColumn('disputes', 'invoice_id')) {
                $table->dropForeign(['invoice_id']);
                $table->dropColumn('invoice_id');
            }
        });

        Schema::table('replacements', function (Blueprint $table) {
            if (Schema::hasColumn('replacements', 'disputed_at')) {
                $table->dropColumn('disputed_at');
            }
            if (Schema::hasColumn('replacements', 'rejected_at')) {
                $table->dropColumn('rejected_at');
            }
            if (Schema::hasColumn('replacements', 'rejection_evidence')) {
                $table->dropColumn('rejection_evidence');
            }
            if (Schema::hasColumn('replacements', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
            if (Schema::hasColumn('replacements', 'warranty_claim_id')) {
                $table->dropForeign(['warranty_claim_id']);
                $table->dropColumn('warranty_claim_id');
            }
        });

        Schema::table('returns', function (Blueprint $table) {
            if (Schema::hasColumn('returns', 'rejection_evidence')) {
                $table->dropColumn('rejection_evidence');
            }
            if (Schema::hasColumn('returns', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
            if (Schema::hasColumn('returns', 'warranty_claim_id')) {
                $table->dropForeign(['warranty_claim_id']);
                $table->dropColumn('warranty_claim_id');
            }
        });

        Schema::dropIfExists('warranty_claims');
    }
};
