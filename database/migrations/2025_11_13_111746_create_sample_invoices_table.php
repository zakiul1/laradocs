<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sample_invoices', function (Blueprint $table) {
            $table->id();

            // Human-readable invoice number (unique, sequential)
            $table->unsignedBigInteger('invoice_no')->unique();

            // Relations
            $table->foreignId('shipper_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();

            // Courier is also a company (category = Courier Service)
            $table->foreignId('courier_company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            // Snapshots
            $table->string('shipper_name');
            $table->text('shipper_address')->nullable();

            $table->string('customer_company_name')->nullable();
            $table->text('customer_address_block')->nullable();
            $table->string('attention_name')->nullable();

            // Details
            $table->string('buyer_account')->nullable();
            $table->enum('shipment_terms', ['Collect', 'Prepaid'])->default('Collect');
            $table->string('courier_name')->nullable(); // snapshot of courier company name
            $table->string('tracking_number')->nullable();

            // Footer text
            $table->text('footer_note')->nullable();

            // Calculated
            $table->decimal('total_amount', 15, 2)->default(0);

            // Meta
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sample_invoices');
    }
};