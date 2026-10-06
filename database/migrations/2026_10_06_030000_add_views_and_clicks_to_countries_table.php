<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('countries')) {
            Schema::table('countries', function (Blueprint $table) {
                if (! Schema::hasColumn('countries', 'views')) {
                    $table->decimal('views', 10, 4)->default(0.0000); // cost per view/impression
                }
                if (! Schema::hasColumn('countries', 'clicks')) {
                    $table->decimal('clicks', 10, 2)->default(0.00); // cost per click
                }
            });
        }

        if (Schema::hasTable('promotion_plans')) {
            Schema::dropIfExists('promotion_plans');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('countries')) {
            Schema::table('countries', function (Blueprint $table) {
                if (Schema::hasColumn('countries', 'views')) {
                    $table->dropColumn('views');
                }
                if (Schema::hasColumn('countries', 'clicks')) {
                    $table->dropColumn('clicks');
                }
            });
        }
    }
};
