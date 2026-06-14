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
    Schema::create('perlengkapan_fisiks', function (Blueprint $table) {

        $table->id();

        $table->string('nama');

        $table->text('deskripsi');

        $table->string('badge')->nullable();

        $table->longText('fitur');

        $table->string('gambar');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perlengkapan_fisiks');
    }
};
