<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_jobs', function (Blueprint $table) {
            $table->id();

            // The customer and the person performing the service
            $table->foreignId('customer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('provider_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // The platform item being serviced, if it exists.
            // Nullable because the item may not be registered on Parts & Parcel.
            $table->foreignId('item_id')
                ->nullable()
                ->constrained('items')
                ->nullOnDelete();

            // Used when the serviced item does not exist in the platform catalog.
            $table->text('external_item_description')->nullable();

            // Commercial records that led to this service.
            // A service may originate from an accepted offer.
            $table->foreignId('offer_id')
                ->nullable()
                ->constrained('offers')
                ->nullOnDelete();

            $table->foreignId('invoice_id')
                ->nullable()
                ->constrained('invoices')
                ->nullOnDelete();

            // What is actually being done.
            $table->string('title');
            $table->text('description')->nullable();

            $table->string('status')->default('pending');
            // pending | scheduled | in_progress | completed | cancelled | disputed

            // Where/when the service is expected to happen.
            $table->foreignId('location_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete();

            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Agreed service warranty.
            $table->unsignedInteger('warranty_period_days')->nullable();
            $table->text('warranty_terms')->nullable();
            $table->timestamp('warranty_starts_at')->nullable();
            $table->timestamp('warranty_ends_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['provider_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['item_id', 'status']);
            $table->index(['offer_id']);
            $table->index(['invoice_id']);
        });

        Schema::create('service_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_job_id')
                ->constrained('service_jobs')
                ->cascadeOnDelete();

            $table->foreignId('reviewer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('provider_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('rating');

            $table->text('review')->nullable();

            $table->timestamps();

            $table->unique(['service_job_id', 'reviewer_id']);
            $table->index(['provider_id', 'rating']);
        });
    }

    public function down(): void { 
        Schema::dropIfExists('shipment_items');
        Schema::dropIfExists('shipments');
    }
};
