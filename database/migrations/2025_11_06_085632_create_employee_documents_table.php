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
            $table->string('phone');                // BD format validated in controller
            $table->string('email')->unique();
            $table->string('photo_path')->nullable(); // storage path
            $table->string('address')->nullable();
            $table->date('join_date')->nullable();
            $table->date('leave_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};