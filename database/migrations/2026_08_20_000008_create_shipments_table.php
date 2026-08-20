<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('provider_name')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('origin_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('destination_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->decimal('fee', 15, 2)->default(0);
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['type', 'status']);
            $table->index('tracking_number');
        });

        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->morphs('itemable');
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void { 
        Schema::dropIfExists('shipment_items');
        Schema::dropIfExists('shipments');
    }
};
