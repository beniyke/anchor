<?php

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class AddPerformanceIndex extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // User table indexes
        Schema::whenTableDoesntHaveIndex('user', 'user_status_index', function (SchemaBuilder $table) {
            $table->index('status', 'user_status_index');
        });
        Schema::whenTableDoesntHaveIndex('user', 'user_role_id_index', function (SchemaBuilder $table) {
            $table->index('role_id', 'user_role_id_index');
        });
        Schema::whenTableDoesntHaveIndex('user', 'user_activation_token_index', function (SchemaBuilder $table) {
            $table->index('activation_token', 'user_activation_token_index');
        });
        Schema::whenTableDoesntHaveIndex('user', 'user_reset_token_index', function (SchemaBuilder $table) {
            $table->index('reset_token', 'user_reset_token_index');
        });
        Schema::whenTableDoesntHaveIndex('user', 'user_status_created_at_index', function (SchemaBuilder $table) {
            $table->index(['status', 'created_at'], 'user_status_created_at_index');
        });
        Schema::whenTableDoesntHaveIndex('user', 'user_created_at_index', function (SchemaBuilder $table) {
            $table->index('created_at', 'user_created_at_index');
        });

        // Session table indexes
        Schema::whenTableDoesntHaveIndex('session', 'session_expire_at_index', function (SchemaBuilder $table) {
            $table->index('expire_at', 'session_expire_at_index');
        });
        Schema::whenTableDoesntHaveIndex('session', 'session_user_expire_index', function (SchemaBuilder $table) {
            $table->index(['user_id', 'expire_at'], 'session_user_expire_index');
        });
        Schema::whenTableDoesntHaveIndex('session', 'session_created_at_index', function (SchemaBuilder $table) {
            $table->index('created_at', 'session_created_at_index');
        });

        // Notification table indexes
        Schema::whenTableDoesntHaveIndex('notification', 'notification_user_read_index', function (SchemaBuilder $table) {
            $table->index(['user_id', 'is_read'], 'notification_user_read_index');
        });
        Schema::whenTableDoesntHaveIndex('notification', 'notification_user_created_index', function (SchemaBuilder $table) {
            $table->index(['user_id', 'created_at'], 'notification_user_created_index');
        });
        Schema::whenTableDoesntHaveIndex('notification', 'notification_is_read_index', function (SchemaBuilder $table) {
            $table->index('is_read', 'notification_is_read_index');
        });
        Schema::whenTableDoesntHaveIndex('notification', 'notification_created_at_index', function (SchemaBuilder $table) {
            $table->index('created_at', 'notification_created_at_index');
        });

        // Activity table indexes
        Schema::whenTableDoesntHaveIndex('activity', 'activity_user_created_index', function (SchemaBuilder $table) {
            $table->index(['user_id', 'created_at'], 'activity_user_created_index');
        });
        Schema::whenTableDoesntHaveIndex('activity', 'activity_created_at_index', function (SchemaBuilder $table) {
            $table->index('created_at', 'activity_created_at_index');
        });

        // Role table indexes
        Schema::whenTableDoesntHaveIndex('role', 'role_type_index', function (SchemaBuilder $table) {
            $table->index('type', 'role_type_index');
        });
        Schema::whenTableDoesntHaveIndex('role', 'role_access_index', function (SchemaBuilder $table) {
            $table->index('access', 'role_access_index');
        });
        Schema::whenTableDoesntHaveIndex('role', 'role_type_access_index', function (SchemaBuilder $table) {
            $table->index(['type', 'access'], 'role_type_access_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // User table indexes
        Schema::whenTableHasIndex('user', 'user_status_index', function (SchemaBuilder $table) {
            $table->dropIndex('user_status_index');
        });
        Schema::whenTableHasIndex('user', 'user_role_id_index', function (SchemaBuilder $table) {
            $table->dropIndex('user_role_id_index');
        });
        Schema::whenTableHasIndex('user', 'user_activation_token_index', function (SchemaBuilder $table) {
            $table->dropIndex('user_activation_token_index');
        });
        Schema::whenTableHasIndex('user', 'user_reset_token_index', function (SchemaBuilder $table) {
            $table->dropIndex('user_reset_token_index');
        });
        Schema::whenTableHasIndex('user', 'user_status_created_at_index', function (SchemaBuilder $table) {
            $table->dropIndex('user_status_created_at_index');
        });
        Schema::whenTableHasIndex('user', 'user_created_at_index', function (SchemaBuilder $table) {
            $table->dropIndex('user_created_at_index');
        });

        // Session table indexes
        Schema::whenTableHasIndex('session', 'session_expire_at_index', function (SchemaBuilder $table) {
            $table->dropIndex('session_expire_at_index');
        });
        Schema::whenTableHasIndex('session', 'session_user_expire_index', function (SchemaBuilder $table) {
            $table->dropIndex('session_user_expire_index');
        });
        Schema::whenTableHasIndex('session', 'session_created_at_index', function (SchemaBuilder $table) {
            $table->dropIndex('session_created_at_index');
        });

        // Notification table indexes
        Schema::whenTableHasIndex('notification', 'notification_user_read_index', function (SchemaBuilder $table) {
            $table->dropIndex('notification_user_read_index');
        });
        Schema::whenTableHasIndex('notification', 'notification_user_created_index', function (SchemaBuilder $table) {
            $table->dropIndex('notification_user_created_index');
        });
        Schema::whenTableHasIndex('notification', 'notification_is_read_index', function (SchemaBuilder $table) {
            $table->dropIndex('notification_is_read_index');
        });
        Schema::whenTableHasIndex('notification', 'notification_created_at_index', function (SchemaBuilder $table) {
            $table->dropIndex('notification_created_at_index');
        });

        // Activity table indexes
        Schema::whenTableHasIndex('activity', 'activity_user_created_index', function (SchemaBuilder $table) {
            $table->dropIndex('activity_user_created_index');
        });
        Schema::whenTableHasIndex('activity', 'activity_created_at_index', function (SchemaBuilder $table) {
            $table->dropIndex('activity_created_at_index');
        });

        // Role table indexes
        Schema::whenTableHasIndex('role', 'role_type_index', function (SchemaBuilder $table) {
            $table->dropIndex('role_type_index');
        });
        Schema::whenTableHasIndex('role', 'role_access_index', function (SchemaBuilder $table) {
            $table->dropIndex('role_access_index');
        });
        Schema::whenTableHasIndex('role', 'role_type_access_index', function (SchemaBuilder $table) {
            $table->dropIndex('role_type_access_index');
        });
    }
}
