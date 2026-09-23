<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('receiver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('provider_name')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('origin_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('origin_contact_name')->nullable();
            $table->string('origin_contact_phone')->nullable();
            $table->string('origin_address_line_1');
            $table->string('origin_address_line_2')->nullable();
            $table->string('origin_city');
            $table->string('origin_state')->nullable();
            $table->string('origin_country')->default('Nigeria');
            $table->string('origin_postal_code')->nullable();
            $table->decimal('origin_latitude', 10, 7)->nullable();
            $table->decimal('origin_longitude', 10, 7)->nullable();
            $table->foreignId('destination_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('destination_contact_name')->nullable();
            $table->string('destination_contact_phone')->nullable();
            $table->string('destination_address_line_1');
            $table->string('destination_address_line_2')->nullable();
            $table->string('destination_city');
            $table->string('destination_state')->nullable();
            $table->string('destination_country')->default('Nigeria');
            $table->string('destination_postal_code')->nullable();
            $table->decimal('destination_latitude', 10, 7)->nullable();
            $table->decimal('destination_longitude', 10, 7)->nullable();
            $table->decimal('fee', 15, 2)->default(0);
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('tracking_number');
        });

        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('itemable');
            $table->string('description'); //listing name or item description 
            $table->unsignedInteger('quantity')->default(1);
            $table->string('instruction')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { 
        Schema::dropIfExists('shipment_items');
        Schema::dropIfExists('shipments');
    }
};
