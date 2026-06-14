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
        Schema::create('infaq_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255); // Judul/Nama pos infaq, contoh: "Operasional Masjid"
            $table->decimal('dana_dibutuhkan', 15, 2); // Dana yang dibutuhkan
            $table->string('slug', 255)->unique(); // Slug untuk URL
            $table->text('description')->nullable(); // Deskripsi singkat mengenai pos alokasi dana
            $table->string('category')->default('umum'); // Kategori untuk membantu logika: 'operasional', 'sosial', 'pembangunan', 'bencana'
            $table->boolean('is_active')->default(true); // Status untuk mengaktifkan/menonaktifkan pos ini dari pilihan otomatis
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('infaq_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_id')->constrained('toko')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('infaq_list_id')->constrained('infaq_lists')->onDelete('cascade');

            $table->decimal('amount', 15, 2);
            $table->string('status')->default('pending');
            $table->string('donor_name')->default('Hamba Allah');
            $table->text('note')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('selling_id')->constrained('toko_selling')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('infaq_image', function (Blueprint $table) {
            $table->id();
            $table->foreignId('infaq_list_id')->constrained('infaq_lists')->onDelete('cascade');
            $table->boolean('is_main')->default(false);
            $table->string('image_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infaq_histories');
        Schema::dropIfExists('infaq_lists');
        Schema::dropIfExists('infaq_image');
    }
};
