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
        Schema::create('jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('level')->default(1); // Level hierarki
            $table->text('description')->nullable();
            $table->boolean('can_invite_users')->default(false);
            $table->boolean('can_manage_inventory')->default(false);
            $table->boolean('can_view_reports')->default(false);
            $table->boolean('can_manage_orders')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatan');
    }
};
