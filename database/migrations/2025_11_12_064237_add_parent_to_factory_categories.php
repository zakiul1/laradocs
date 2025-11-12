<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('factory_categories', function (Blueprint $table) {
            // parent id (nullable so existing rows are valid; children will set this)
            if (!Schema::hasColumn('factory_categories', 'factory_category_id')) {
                $table->foreignId('factory_category_id')
                    ->nullable()
                    ->constrained('factory_categories')
                    ->cascadeOnDelete(); // delete children if parent is deleted
                $table->index(['factory_category_id', 'position']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('factory_categories', function (Blueprint $table) {
            if (Schema::hasColumn('factory_categories', 'factory_category_id')) {
                $table->dropForeign(['factory_category_id']);
                $table->dropIndex(['factory_category_id', 'position']);
                $table->dropColumn('factory_category_id');
            }
        });
    }
};