<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_dynamic_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('dynamic_field_id')->constrained('dynamic_fields')->cascadeOnDelete();
            $table->string('field_type', 30);
            $table->text('value_text')->nullable();
            $table->decimal('value_number', 15, 2)->nullable();
            $table->json('value_json')->nullable();
            $table->timestamps();

            $table->unique(['service_id', 'dynamic_field_id']);
            $table->index('field_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_dynamic_field_values');
    }
};

