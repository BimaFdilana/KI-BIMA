<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artikels', function (Blueprint $table) {

            $table->timestamp('published_at')->nullable();
            $table->integer('views')->default(0);

        });
    }

    public function down(): void
    {
        Schema::table('artikels', function (Blueprint $table) {

            $table->dropColumn(['published_at', 'views']);

        });
    }
};