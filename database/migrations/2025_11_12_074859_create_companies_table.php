<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();          // short internal name / code
            $table->string('company_name');              // full legal / display name

            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->text('address')->nullable();         // textarea

            $table->string('contact_person')->nullable();
            $table->string('website')->nullable();

            $table->text('note')->nullable();

            $table->foreignId('company_category_id')
                ->nullable()
                ->constrained('company_categories')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['company_name']);
            $table->index(['email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};