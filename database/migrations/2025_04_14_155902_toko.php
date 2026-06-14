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
        Schema::create('toko', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('token')->default('0');
            $table->integer('ki_point')->default('0');
            $table->text('address');
            $table->string('rek_number')->nullable();
            $table->string('rek_name')->nullable();
            $table->string('rek_bank')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->enum('type', ['ki', 'kmp', 'pro'])->default('ki');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'active', 'suspend', 'rejected'])->default('pending');
            $table->dateTime('verified_at')->nullable();
            $table->foreignId('verified_by')->constrained('users')->onDelete('cascade')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->dateTime('edited_at')->nullable();
            $table->foreignId('edited_by')->constrained('users')->onDelete('cascade')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toko');
    }
};
