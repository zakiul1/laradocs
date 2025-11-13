<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('banks', function (Blueprint $table) {
            // New fields
            $table->string('swift_code')->nullable()->after('email');
            $table->string('bank_account')->nullable()->after('swift_code');

            // Polymorphic company reference (Customer or Shipper)
            $table->unsignedBigInteger('company_id')->nullable()->after('type');
            $table->string('company_type')->nullable()->after('company_id'); // 'customer' | 'shipper'

            // Helpful indexes
            $table->index(['company_type', 'company_id'], 'banks_company_poly_idx');
            $table->index('swift_code');
            $table->index('bank_account');
        });
    }

    public function down(): void
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->dropIndex('banks_company_poly_idx');
            $table->dropColumn(['swift_code', 'bank_account', 'company_id', 'company_type']);
        });
    }
};