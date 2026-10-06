<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add escrow_fee to payments table
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'escrow_fee')) {
                $table->decimal('escrow_fee', 15, 2)->default(0.00)->after('amount');
            }
        });

        // 2. Add amount, currency, and payment_id to settlements table
        Schema::table('settlements', function (Blueprint $table) {
            if (! Schema::hasColumn('settlements', 'amount')) {
                $table->decimal('amount', 15, 2)->default(0.00);
            }
            if (! Schema::hasColumn('settlements', 'currency')) {
                $table->string('currency', 3)->default('NGN');
            }
            if (! Schema::hasColumn('settlements', 'payment_id')) {
                $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            }
        });

        // 3. Migrate existing settlement net_amount to amount
        if (Schema::hasColumn('settlements', 'net_amount')) {
            DB::statement("UPDATE settlements SET amount = net_amount WHERE (amount = 0 OR amount IS NULL) AND net_amount > 0");
        }

        // 4. Drop unnecessary columns from settlements
        Schema::table('settlements', function (Blueprint $table) {
            $colsToDrop = [];
            foreach (['gross_amount', 'commission', 'refunds', 'net_amount'] as $col) {
                if (Schema::hasColumn('settlements', $col)) {
                    $colsToDrop[] = $col;
                }
            }
            if (! empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settlements', function (Blueprint $table) {
            if (! Schema::hasColumn('settlements', 'gross_amount')) {
                $table->decimal('gross_amount', 15, 2)->default(0);
                $table->decimal('commission', 15, 2)->default(0);
                $table->decimal('refunds', 15, 2)->default(0);
                $table->decimal('net_amount', 15, 2)->default(0);
            }
            if (Schema::hasColumn('settlements', 'payment_id')) {
                $table->dropColumn('payment_id');
            }
            if (Schema::hasColumn('settlements', 'amount')) {
                $table->dropColumn('amount');
            }
            if (Schema::hasColumn('settlements', 'currency')) {
                $table->dropColumn('currency');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'escrow_fee')) {
                $table->dropColumn('escrow_fee');
            }
        });
    }
};
