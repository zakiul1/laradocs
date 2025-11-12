<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('factory_subcategories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->foreignId('factory_category_id')->constrained('factory_categories')->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['factory_category_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factory_subcategories');
    }
};