<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fix: price_buy was decimal(8,2) with max 999,999.99
     * Value 1,169,863 caused "Out of range" error.
     * Increased to decimal(15,2) to support up to 9,999,999,999,999.99
     */
    public function up(): void
    {
        Schema::table('barang_toko', function (Blueprint $table) {
            $table->decimal('price_buy', 15, 2)->change();
            $table->decimal('price_sell', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_toko', function (Blueprint $table) {
            $table->decimal('price_buy', 8, 2)->change();
            $table->decimal('price_sell', 8, 2)->nullable()->change();
        });
    }
};
