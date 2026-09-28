<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add item_id and slug columns to listings table if not present
        Schema::table('listings', function (Blueprint $table) {
            if (!Schema::hasColumn('listings', 'item_id')) {
                $table->unsignedBigInteger('item_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('listings', 'slug')) {
                $table->string('slug')->nullable()->after('item_id');
            }
        });

        // 2. Migrate existing data from assetable_id to item_id, and generate alphabetical slug
        if (Schema::hasColumn('listings', 'assetable_id')) {
            $listings = DB::table('listings')->get();
            foreach ($listings as $listing) {
                $itemId = $listing->assetable_id ?? $listing->item_id ?? null;
                $slug = null;

                if ($itemId) {
                    $itemName = DB::table('items')->where('id', $itemId)->value('name');
                    $baseSlug = Str::slug($itemName ?: "listing-{$listing->id}");
                } else {
                    $baseSlug = "listing-{$listing->id}";
                }

                // Ensure unique slug
                $candidate = $baseSlug;
                $counter = 1;
                while (DB::table('listings')->where('slug', $candidate)->where('id', '!=', $listing->id)->exists()) {
                    $candidate = "{$baseSlug}-{$counter}";
                    $counter++;
                }

                DB::table('listings')->where('id', $listing->id)->update([
                    'item_id' => $itemId,
                    'slug' => $candidate,
                ]);
            }
        }

        // 3. Drop index on assetable columns before dropping columns (required for SQLite)
        try {
            DB::statement('DROP INDEX IF EXISTS listings_assetable_type_assetable_id_index');
        } catch (\Throwable $e) {
            // Ignore if index doesn't exist
        }

        // 4. Drop assetable_type and assetable_id columns if they still exist
        Schema::table('listings', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('listings', 'assetable_type')) {
                $columnsToDrop[] = 'assetable_type';
            }
            if (Schema::hasColumn('listings', 'assetable_id')) {
                $columnsToDrop[] = 'assetable_id';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        // 5. Add indexes on item_id and slug
        Schema::table('listings', function (Blueprint $table) {
            $table->index('item_id');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->string('assetable_type')->nullable()->after('user_id');
            $table->unsignedBigInteger('assetable_id')->nullable()->after('assetable_type');
        });

        $listings = DB::table('listings')->get();
        foreach ($listings as $listing) {
            DB::table('listings')->where('id', $listing->id)->update([
                'assetable_type' => 'App\\Models\\Item',
                'assetable_id' => $listing->item_id,
            ]);
        }

        try {
            Schema::table('listings', function (Blueprint $table) {
                $table->dropIndex(['item_id']);
                $table->dropIndex(['slug']);
            });
        } catch (\Throwable $e) {}

        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['item_id', 'slug']);
        });
    }
};
