<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete();

            $table->foreignId('initiator_business_account_id')
                ->constrained('business_accounts')
                ->cascadeOnDelete();

            $table->foreignId('recipient_business_account_id')
                ->constrained('business_accounts')
                ->cascadeOnDelete();

            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['initiator_business_account_id', 'last_message_at'], 'conv_init_last_idx');
            $table->index(['recipient_business_account_id', 'last_message_at'], 'conv_recv_last_idx');
            $table->index(['service_id', 'last_message_at'], 'conv_service_last_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
