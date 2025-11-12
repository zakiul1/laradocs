<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable()->unique();
            $table->string('designation')->nullable();

            $table->date('join_date');
            $table->date('leave_date')->nullable();

            $table->string('photo')->nullable(); // path relative to storage
            $table->json('documents')->nullable(); // array of paths

            $table->text('address')->nullable();
            $table->string('alternative_contact_number')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();

            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->string('blood_group')->nullable();

            $table->enum('status', ['Active', 'Inactive', 'Resigned'])->default('Active');

            $table->text('notes')->nullable();

            $table->softDeletes(); // allows soft delete (with optional hard delete)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};