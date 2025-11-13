<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sample_invoice_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sample_invoice_id')
                ->constrained('sample_invoices')
                ->cascadeOnDelete();

            $table->string('art_num')->nullable();
            $table->string('description')->nullable();
            $table->string('size')->nullable();
            $table->string('hs_code')->nullable();
            $table->unsignedInteger('qty')->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('sub_total', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sample_invoice_items');
    }
};