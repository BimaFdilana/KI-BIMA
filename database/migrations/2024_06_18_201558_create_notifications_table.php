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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->string('notifiable_type');
            $table->uuid('notifiable_id');
            $table->text('data');
            $table->string('sender_type')->nullable();
            $table->uuid('sender_id')->nullable();
            $table->string('path')->nullable();
            $table->enum('status', ['unopen', 'unread', 'read', 'clicked', 'downloaded'])->default('unopen');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->boolean('is_important')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id'], 'notif_notifiable_type_id_index');
            $table->index(['sender_type', 'sender_id'], 'notif_sender_type_id_index');
            $table->index('status', 'notif_status_index');
            $table->index('is_active', 'notif_is_active_index');
            $table->index('is_system', 'notif_is_system_index');
            $table->index('is_important', 'notif_is_important_index');
        });

        Schema::create('notification_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('notification_id');
            $table->string('group_type'); // role, permission, custom
            $table->string('group_id');
            $table->timestamps();

            $table->foreign('notification_id', 'ng_notification_id_foreign')
                ->references('id')
                ->on('notifications')
                ->onDelete('cascade');

            $table->index(['group_type', 'group_id'], 'ng_group_type_id_index');
        });

        Schema::create('notification_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_type');
            $table->uuid('user_id');
            $table->string('notification_type');
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_email_enabled')->default(false);
            $table->boolean('is_push_enabled')->default(false);
            $table->timestamps();

            $table->index(['user_type', 'user_id'], 'ns_user_type_id_index');
            $table->unique(['user_type', 'user_id', 'notification_type'], 'ns_user_notif_type_unique');
        });

        Schema::create('notification_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_type');
            $table->uuid('user_id');
            $table->string('subscribable_type');
            $table->uuid('subscribable_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_type', 'user_id'], 'nsub_user_type_id_index');
            $table->index(['subscribable_type', 'subscribable_id'], 'nsub_subscribable_type_id_index');
            $table->unique(
                ['user_type', 'user_id', 'subscribable_type', 'subscribable_id'],
                'nsub_user_subscribable_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_subscriptions');
        Schema::dropIfExists('notification_settings');
        Schema::dropIfExists('notification_groups');
        Schema::dropIfExists('notifications');
    }
};
