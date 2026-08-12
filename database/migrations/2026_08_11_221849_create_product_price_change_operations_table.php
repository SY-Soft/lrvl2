<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_price_change_operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('cents');
            $table->unsignedInteger('total')->default(0);
            $table->unsignedInteger('processed')->default(0);
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_price_change_operations');
    }
};
