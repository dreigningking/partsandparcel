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
        Schema::table('listings', function (Blueprint $table) {
            $table->boolean('is_negotiable')->default(false)->after('price');
            $table->boolean('is_warranty_negotiable')->default(false)->after('warranty_period_days');
            $table->boolean('allow_shipping')->default(false)->after('warranty_terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['is_negotiable', 'is_warranty_negotiable', 'allow_shipping']);
        });
    }
};
