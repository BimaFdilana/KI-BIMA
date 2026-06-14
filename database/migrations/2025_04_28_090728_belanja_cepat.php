<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tabel kriteria
        Schema::create('kriteria', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->integer('prioritas');
            $table->float('bobot', 5, 2);
            $table->timestamps();
        });

        // Tabel subkriteria
        Schema::create('subkriteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kriteria_id')->constrained('kriteria')->onDelete('cascade');
            $table->string('nama');
            $table->integer('prioritas');
            $table->float('bobot', 5, 2);
            $table->timestamps();
        });

        // Tabel penilaian
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_id')->nullable()->constrained('toko')->onDelete('cascade');
            $table->foreignId('barangki_id')->constrained('barang_ki')->onDelete('cascade');
            $table->foreignId('subkriteria_id')->constrained('subkriteria')->onDelete('cascade');
            $table->integer('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penilaian');
        Schema::dropIfExists('subkriteria');
        Schema::dropIfExists('kriteria');
    }
};
