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
        Schema::create('products', function (Blueprint $table) {

            $table->id();

            $table->string('nama');

            $table->string('slug')->unique();

            $table->enum('kategori', [
                'perlengkapan_fisik',
                'plugin',
                'ebook'
            ]);

            $table->string('subtitle')->nullable();

            $table->text('deskripsi');

            $table->string('badge')->nullable();

            $table->string('gambar')->nullable();

            $table->json('fitur')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};