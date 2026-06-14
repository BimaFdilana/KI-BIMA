<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('information_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('informations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->text('content')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('information_categories')->onDelete('set null');
            $table->enum('visibility', ['public',  'private', 'listed'])->default('public');
            $table->integer('shares_count')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
            $table->index(['category_id', 'visibility']);
        });

        Schema::create('information_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('information_id')->constrained('informations')->onDelete('cascade');
            $table->enum('type', ['image', 'video'])->default('image');
            $table->string('media_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('alt_text')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->index(['information_id', 'order']);
        });

        Schema::create('information_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('information_id')->constrained('informations')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('device_id')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('information_comments', 'id')->onDelete('cascade');
            $table->text('content');
            $table->integer('replies_count')->default(0);
            $table->timestamps();
            $table->index(['information_id', 'parent_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('information_comments');
        Schema::dropIfExists('information_media');
        Schema::dropIfExists('informations');
        Schema::dropIfExists('information_categories');
    }
};
