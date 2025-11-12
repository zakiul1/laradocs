<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();              // like 100239
            $table->enum('type', ['LC', 'TT'])->default('LC');   // UI toggle
            $table->foreignId('shipper_id')->constrained('shippers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnUpdate()->restrictOnDelete();

            // Header meta
            $table->date('issue_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('payment_mode')->nullable();          // free text: "Transferable L/C at sight", "Telegraphic Transfer", etc.
            $table->string('terms_of_shipment')->nullable();     // free text
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();

            // Money
            $table->decimal('items_total', 16, 2)->default(0);
            $table->decimal('commercial_cost', 16, 2)->default(0); // applies once per invoice
            $table->decimal('siatex_discount', 16, 2)->default(0); // applied last
            $table->decimal('grand_total', 16, 2)->default(0);

            // Text blocks
            $table->text('terms_and_conditions')->nullable();    // defaulted by type (LC vs TT), editable

            // Audit
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('sales_invoices');
    }
};
