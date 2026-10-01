<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('locations')) {
            Schema::create('locations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('label');
                $table->string('contact_name')->nullable();
                $table->string('phone')->nullable();
                $table->string('address_line_1');
                $table->string('address_line_2')->nullable();
                $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
                $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
                $table->string('city');
                $table->string('postal_code')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->boolean('is_default')->default(false);
                $table->timestamps();
                $table->index(['user_id', 'is_default']);
            });
        }

        if (! Schema::hasTable('bank_accounts')) {
            Schema::create('bank_accounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('bank_name');
                $table->string('bank_code');
                $table->string('account_number');
                $table->string('account_name');
                $table->string('recipient_code')->nullable();
                $table->string('currency', 3)->default('NGN');
                $table->boolean('is_default')->default(false);
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'is_default']);
            });
        }

        // Device Tokens for FCM Push Notifications
        if (! Schema::hasTable('device_tokens')) {
            Schema::create('device_tokens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('token')->unique();
                $table->string('platform')->default('web');
                $table->string('device_name')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamps();
                $table->index('user_id');
            });
        }
    }

    public function down(): void { 
        Schema::dropIfExists('locations'); 
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('device_tokens');
    }
};
