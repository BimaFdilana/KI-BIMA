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
        // Tabel satuan_items
        Schema::create(
            'satuan_items',
            function (Blueprint $table) {
                $table->id();
                $table->integer('level')->default('1'); // Menentukan level hierarki satuan
                $table->string('name'); // Nama satuan
                $table->string('cut_name'); // Singkatan satuan
                $table->string('type'); // Tipe satuan (berat, panjang, volume, dll.)
                $table->enum('selling', ['true', 'false', 'except'])->default('true');
                $table->string('description')->nullable(); // Deskripsi satuan
                $table->timestamps();
            }
        );


        // Tabel categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('photo')->nullable();
        });

        // Tabel subcategories
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->string('photo')->nullable();
            $table->decimal('margin', 5, 2)->default('0');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });

        // Tabel brands
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('photo')->nullable();
        });


        // Membuat tabel tipe_barang
        Schema::create('type_barang', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama tipe barang
            $table->text('description'); // Deskripsi tipe barang
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('sub_categories');
        Schema::dropIfExists('satuan_items');
        Schema::dropIfExists('type_barang');
    }
};
