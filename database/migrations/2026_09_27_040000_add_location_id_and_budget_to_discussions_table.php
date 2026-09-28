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
        Schema::table('discussions', function (Blueprint $table) {
            if (!Schema::hasColumn('discussions', 'location_id')) {
                $table->foreignId('location_id')->nullable()->after('model_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('discussions', 'budget')) {
                $table->string('budget')->nullable()->after('location_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discussions', function (Blueprint $table) {
            if (Schema::hasColumn('discussions', 'location_id')) {
                $table->dropForeign(['location_id']);
                $table->dropColumn(['location_id']);
            }
            if (Schema::hasColumn('discussions', 'budget')) {
                $table->dropColumn(['budget']);
            }
        });
    }
};
