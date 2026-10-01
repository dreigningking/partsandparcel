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
        Schema::create('viewed_entities', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->string('user_agent'); //
            $table->string('device_type'); // mobile, desktop, tablet
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('viewable_id');
            $table->string('viewable_type'); //listing, post, discussion, user profile
            $table->timestamps();
            $table->index('user_id');
            $table->index(['viewable_id', 'viewable_type']);
            $table->unique(['user_id', 'viewable_id', 'viewable_type']);
        });

        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->unsignedBigInteger('watchable_id');//listing, post, discussion
            $table->string('watchable_type');
            $table->timestamps();
            $table->index('user_id');
            $table->index(['watchable_id', 'watchable_type']);
            $table->unique(['user_id', 'watchable_id', 'watchable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viewed_entities');
        Schema::dropIfExists('watchlists');
    }
};
