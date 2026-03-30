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
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_account_id')
                ->constrained('business_accounts')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->foreignId('subcategory_id')
                ->constrained('subcategories')
                ->restrictOnDelete();

            $table->foreignId('city_id')
                ->constrained('cities')
                ->restrictOnDelete();

            $table->json('title');
            $table->json('description')->nullable();

            $table->enum('service_type', ['sale', 'rent']);
            $table->decimal('price', 12, 2);
            $table->string('currency', 10)->default('USD');

            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();

            $table->foreignId('reviewed_by_admin_id')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('published_at')->nullable();

            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->unsignedBigInteger('views_count')->default(0);

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->softDeletes();
            $table->timestamps();

            $table->index('business_account_id');
            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index('city_id');
            $table->index('status');
            $table->index('service_type');
            $table->index('is_active');
            $table->index('published_at');
            $table->index(['category_id', 'subcategory_id']);
            $table->index(['city_id', 'status', 'is_active']);
            $table->index(['business_account_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
