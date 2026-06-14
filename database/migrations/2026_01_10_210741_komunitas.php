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
        // Table posts
        Schema::create('komunitas_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        // Table post media
        Schema::create('komunitas_post_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('komunitas_post')->cascadeOnDelete();
            $table->string('file_path');
            $table->enum('type', ['image', 'video'])->default('image');
            $table->integer('order')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        // Table likes
        Schema::create('komunitas_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('post_id')->constrained('komunitas_post')->cascadeOnDelete();
            $table->unique(['user_id', 'post_id']);
            $table->timestamps();
        });

        // Table comments
        Schema::create('komunitas_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('post_id')->constrained('komunitas_post')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('komunitas_comments')->cascadeOnDelete();
            $table->text('content');
            $table->integer('likes_count')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        // Table comment likes
        Schema::create('komunitas_comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('comment_id')->constrained('komunitas_comments')->cascadeOnDelete();
            $table->unique(['user_id', 'comment_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komunitas_comment_likes');
        Schema::dropIfExists('komunitas_comments');
        Schema::dropIfExists('komunitas_likes');
        Schema::dropIfExists('komunitas_post_media');
        Schema::dropIfExists('komunitas_post');
    }
};
