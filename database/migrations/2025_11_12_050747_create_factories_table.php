<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('factories', function (Blueprint $table) {
            $table->id();

            // Core
            $table->string('name');
            $table->text('address')->nullable();              // textarea in UI
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('registration_no')->nullable();
            $table->unsignedInteger('total_employees')->default(0);
            $table->unsignedInteger('lines')->default(0);
            $table->text('notes')->nullable();

            // Category links
            $table->foreignId('category_id')->nullable()->constrained('factory_categories')->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('factory_subcategories')->nullOnDelete();

            // Audit
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Indexes
            $table->index('name');
            $table->index(['category_id', 'subcategory_id']);
            $table->index(['created_by', 'updated_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factories');
    }
};