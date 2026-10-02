<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moderations', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('moderatable'); //listing, request
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action')->nullable();
            $table->string('status'); //approved, rejected, pending
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['moderatable_type', 'moderatable_id', 'status']);
            $table->index('moderated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderations');
    }
};
