<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete();

            $table->foreignId('requester_business_account_id')
                ->constrained('business_accounts')
                ->cascadeOnDelete();

            $table->foreignId('provider_business_account_id')
                ->constrained('business_accounts')
                ->cascadeOnDelete();

            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled'])
                ->default('pending');

            $table->unsignedInteger('quantity')->default(1);
            $table->timestamp('needed_at')->nullable();
            $table->text('message')->nullable();
            $table->decimal('price_offer', 12, 2)->nullable();

            $table->text('rejection_reason')->nullable();
            $table->timestamp('responded_at')->nullable();

            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['service_id', 'status']);
            $table->index(['requester_business_account_id', 'status']);
            $table->index(['provider_business_account_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};

