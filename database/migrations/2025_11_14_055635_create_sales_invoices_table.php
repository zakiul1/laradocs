<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('shipper_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // Core
            $table->string('invoice_no')->unique();
            $table->enum('invoice_type', ['LC', 'TT']); // LC Invoice / TT Invoice

            $table->date('issue_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('terms_of_shipment')->nullable();
            $table->string('currency_code', 10)->nullable(); // snapshot from Currency

            // Snapshots for layout
            $table->string('shipper_name');
            $table->text('shipper_address');
            $table->string('customer_company_name');
            $table->text('customer_address_block');
            $table->string('attention_name')->nullable();
            $table->text('our_bank_block')->nullable();

            // Totals
            $table->unsignedInteger('total_qty')->default(0);
            $table->decimal('items_total', 15, 2)->default(0);
            $table->decimal('commercial_cost', 15, 2)->default(0);
            $table->decimal('siatex_discount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);

            // Text blocks
            $table->text('terms_and_conditions')->nullable();
            $table->enum('message_type', ['FOB', 'CIF'])->default('FOB');
            $table->text('message_body')->nullable();
            $table->string('footer_note')->nullable(); // “PLEASE ADVISED THE L/C THROUGH OUR BANK AS ABOVE”

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};