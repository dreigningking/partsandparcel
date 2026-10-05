<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('verifications')) {
            Schema::create('verifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('document_type'); // 'passport', 'drivers_license', 'national_id', 'voters_card', 'govt_id'
                $table->string('document_number')->nullable();
                $table->string('front_image')->nullable();
                $table->string('back_image')->nullable();
                $table->string('selfie_image')->nullable();
                $table->json('liveness_images')->nullable(); // multi-frame liveness snapshots sequence
                $table->timestamps();
            });
        }

        Schema::table('locations', function (Blueprint $table) {
            if (!Schema::hasColumn('locations', 'utility_bill_path')) {
                $table->string('utility_bill_path')->nullable()->after('longitude');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifications');

        Schema::table('locations', function (Blueprint $table) {
            if (Schema::hasColumn('locations', 'utility_bill_path')) {
                $table->dropColumn(['utility_bill_path']);
            }
        });
    }
};
