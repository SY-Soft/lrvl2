<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_price_change_operations', function (Blueprint $table) {
            $table
                ->unsignedInteger('errors_to_create')
                ->default(0)
                ->after('failed');
        });
    }

    public function down(): void
    {
        Schema::table('product_price_change_operations', function (Blueprint $table) {
            $table->dropColumn('errors_to_create');
        });
    }
};
