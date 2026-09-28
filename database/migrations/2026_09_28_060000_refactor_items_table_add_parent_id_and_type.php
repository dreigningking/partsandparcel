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
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('user_id')->constrained('items')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('items', 'item_type')) {
                $table->string('item_type')->default('whole')->after('model_id');
            }
            if (Schema::hasColumn('items', 'serial_number')) {
                // SQLite constraint drop safety
                try {
                    $table->dropUnique(['serial_number']);
                } catch (\Exception $e) {}
                $table->dropColumn('serial_number');
            }
        });

        // Migrate components table records into items table if components table exists
        if (Schema::hasTable('components')) {
            $components = DB::table('components')->get();
            foreach ($components as $component) {
                $parentItem = DB::table('items')->where('id', $component->item_id)->first();
                if ($parentItem) {
                    $newItemId = DB::table('items')->insertGetId([
                        'user_id' => $parentItem->user_id,
                        'parent_id' => $parentItem->id,
                        'location_id' => $parentItem->location_id ?? null,
                        'model_id' => $parentItem->model_id,
                        'item_type' => 'part',
                        'name' => $component->name,
                        'condition_status' => $component->condition_status === 'scrap' ? 'faulty' : $component->condition_status,
                        'status' => $component->status ?? 'available',
                        'created_at' => $component->created_at ?? now(),
                        'updated_at' => $component->updated_at ?? now(),
                    ]);

                    DB::table('listings')
                        ->where('assetable_type', 'App\\Models\\Component')
                        ->where('assetable_id', $component->id)
                        ->update([
                            'assetable_type' => 'App\\Models\\Item',
                            'assetable_id' => $newItemId,
                        ]);
                }
            }

            Schema::dropIfExists('components');
        }

        // Clean up remaining Component references in listings if any
        DB::table('listings')
            ->where('assetable_type', 'App\\Models\\Component')
            ->update(['assetable_type' => 'App\\Models\\Item']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn(['parent_id']);
            }
            if (Schema::hasColumn('items', 'item_type')) {
                $table->dropColumn(['item_type']);
            }
            if (!Schema::hasColumn('items', 'serial_number')) {
                $table->string('serial_number')->nullable()->unique();
            }
        });
    }
};
