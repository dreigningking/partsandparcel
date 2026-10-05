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
                $table->boolean('liveness_verified')->default(false);
                $table->string('status')->default('pending'); // 'pending', 'verified', 'rejected'
                $table->text('rejection_reason')->nullable();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('locations', function (Blueprint $table) {
            if (!Schema::hasColumn('locations', 'utility_bill_path')) {
                $table->string('utility_bill_path')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('locations', 'verification_status')) {
                $table->string('verification_status')->default('unverified')->after('utility_bill_path'); // 'unverified', 'pending', 'verified', 'rejected'
            }
            if (!Schema::hasColumn('locations', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('verification_status');
            }
            if (!Schema::hasColumn('locations', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('rejection_reason');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'facial_verified_at')) {
                $table->timestamp('facial_verified_at')->nullable()->after('is_verified');
            }
            if (!Schema::hasColumn('users', 'id_verified_at')) {
                $table->timestamp('id_verified_at')->nullable()->after('facial_verified_at');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifications');

        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['utility_bill_path', 'verification_status', 'rejection_reason', 'verified_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['facial_verified_at', 'id_verified_at']);
        });
    }
};
