<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->morphs('mediable');
            $table->string('collection')->default('default');
            $table->string('name')->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('disk')->default('public');
            $table->string('mime_type')->nullable();
            $table->string('media_type')->default('image'); // 'image', 'video', 'document'
            $table->unsignedBigInteger('size')->nullable(); // in bytes
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('custom_properties')->nullable();
            $table->timestamps();

            $table->index(['mediable_type', 'mediable_id', 'collection']);
            $table->index(['media_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
