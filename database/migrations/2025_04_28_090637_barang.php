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
        // Membuat tabel barang
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->unsignedBigInteger('subcategory_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('type_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'nonactive', 'deleted'])->default('active');
            $table->integer('early_expiry_days')->nullable();
            $table->integer('mid_expiry_days')->nullable();
            $table->enum('buy_by', ['ki', 'kmp'])->default('ki');
            $table->integer('late_expiry_days')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Menambahkan foreign key
            $table->foreign('subcategory_id')->references('id')->on('sub_categories')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('type_barang')->onDelete('cascade'); // Foreign key untuk tipe barang
        });


        Schema::create('barang_ki', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barang_id');
            $table->string('id_barcode')->nullable();
            $table->unsignedBigInteger('satuan_id');
            $table->integer('quantity')->default(0);
            $table->integer('sold_quantity')->default('0');
            $table->decimal('price_buy', 10, 2);
            $table->decimal('price_sell', 10, 2);
            $table->decimal('price_up', 10, 2);
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('discount_percentage', 10, 2)->nullable();
            $table->dateTime('discount_start')->nullable();
            $table->dateTime('discount_end')->nullable();
            $table->dateTime('expired_time')->nullable();
            $table->enum('status', ['active', 'nonactive', 'waiting'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('satuan_id')->references('id')->on('satuan_items')->onDelete('cascade');
            $table->foreign('barang_id')->references('id')->on('barang')->onDelete('cascade');
        });


        Schema::create('barang_toko', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('toko_id');
            $table->unsignedBigInteger('barangki_id');
            $table->integer('quantity')->default(0);
            $table->decimal('price_buy', 8, 2);
            $table->decimal('price_sell', 8, 2)->nullable();
            $table->decimal('price_percentage', 5, 2)->nullable();
            $table->integer('sold')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('toko_id')->references('id')->on('toko')->onDelete('cascade');
            $table->foreign('barangki_id')->references('id')->on('barang_ki')->onDelete('cascade');
        });

        Schema::create('barang_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url');
            $table->unsignedBigInteger('barang_id');
            $table->boolean('is_main')->default(false);
            $table->timestamps();
            $table->foreign('barang_id')->references('id')->on('barang')->onDelete('cascade');
        });


        Schema::create('satuan_conversions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barang_id');
            $table->unsignedBigInteger('from_satuan_id');
            $table->unsignedBigInteger('to_satuan_id');
            $table->decimal('conversion_factor', 15, 5); // Factor untuk konversi: 1 from_satuan = x to_satuan
            $table->timestamps();

            $table->foreign('barang_id')->references('id')->on('barang')->onDelete('cascade');
            $table->foreign('from_satuan_id')->references('id')->on('satuan_items')->onDelete('cascade');
            $table->foreign('to_satuan_id')->references('id')->on('satuan_items')->onDelete('cascade');

            // Unique constraint untuk memastikan tidak ada duplikasi konversi
            $table->unique(['barang_id', 'from_satuan_id', 'to_satuan_id']);
        });

        Schema::create('barang_io', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('barangki_id');
            $table->integer('quantity')->nullable()->default(0);
            $table->integer('price')->nullable()->default(0);
            $table->enum('type', ['in', 'out'])->default('in')->nullable();
            $table->enum('status', ['success', 'failed', 'waiting'])->default('waiting');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('barangki_id')->references('id')->on('barang_ki')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
        Schema::dropIfExists('barang_ki');
        Schema::dropIfExists('barang_toko');
        Schema::dropIfExists('barang_images');
        Schema::dropIfExists('satuan_conversions');
        Schema::dropIfExists('barang_io');
    }
};
