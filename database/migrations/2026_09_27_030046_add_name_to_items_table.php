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
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'location_id')) {
                $table->foreignId('location_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('items', 'name')) {
                $table->string('name')->nullable()->after('model_id');
            }
            if (!Schema::hasColumn('items', 'condition_notes')) {
                $table->text('condition_notes')->nullable()->after('condition_status');
            }
            if (!Schema::hasColumn('items', 'description')) {
                $table->text('description')->nullable()->after('condition_notes');
            }
        });

        if (!Schema::hasColumn('discussions', 'brand_id')) {
            Schema::table('discussions', function (Blueprint $table) {
                $table->foreignId('brand_id')->nullable()->after('category_id')->constrained()->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'location_id')) {
                $table->dropForeign(['location_id']);
                $table->dropColumn(['location_id']);
            }
            $cols = array_filter(['name', 'condition_notes', 'description'], fn($c) => Schema::hasColumn('items', $c));
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        if (Schema::hasColumn('discussions', 'brand_id')) {
            Schema::table('discussions', function (Blueprint $table) {
                $table->dropColumn(['brand_id']);
            });
        }
    }
};
