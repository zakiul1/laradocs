<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sample_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sample_invoice_id')->constrained('sample_invoices')->cascadeOnDelete();
            $table->string('art_num', 100)->nullable();
            $table->string('article_description', 500)->nullable();
            $table->string('size', 100)->nullable();
            $table->string('hs_code', 50)->nullable();
            $table->decimal('qty', 14, 3)->default(0);
            $table->decimal('unit_price', 16, 4)->default(0);
            $table->decimal('sub_total', 16, 2)->default(0);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('sample_invoice_items');
    }
};
