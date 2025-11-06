<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('factories', function (Blueprint $table) {
            $table->id();

            // Core fields
            $table->string('name');                          // Factory name (required in validation)
            $table->string('address', 500)->nullable();      // Optional address
            $table->string('phone', 20)->nullable();         // Optional BD phone (validated in controller)
            $table->unsignedInteger('lines')->default(0);    // Garment line count
            $table->text('notes')->nullable();               // Free-form notes

            // Audit (who created/updated)
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnUpdate();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Helpful indexes (optional but good practice)
            $table->index('name');
            $table->index(['created_by', 'updated_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factories');
    }
};