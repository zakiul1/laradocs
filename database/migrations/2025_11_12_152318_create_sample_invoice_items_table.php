<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sample_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique(); // like 101207

            $table->foreignId('shipper_id')->constrained('shippers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnUpdate()->restrictOnDelete();

            // Details (as per screenshots)
            $table->string('buyer_account')->nullable();    // number
            $table->enum('shipment_terms', ['Collect', 'Prepaid'])->nullable();
            $table->string('courier_name')->nullable();     // DHL/Aramex/FedEx/UPS/...
            $table->string('tracking_number')->nullable();

            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();

            // Totals
            $table->decimal('items_total', 16, 2)->default(0);
            $table->decimal('grand_total', 16, 2)->default(0);

            $table->text('footer_note')->nullable();        // the two bullet notes area
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('sample_invoices');
    }
};
