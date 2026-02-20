<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create the notification table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateNotificationTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::createIfNotExists('notification', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('message');
            $table->string('url')->nullable();
            $table->string('label', 50)->nullable();
            $table->boolean('is_read')->default(0);
            $table->dateTimestamps();

            // Performance Indexes
            $table->index(['user_id', 'is_read'], 'notification_user_read_index');
            $table->index(['user_id', 'created_at'], 'notification_user_created_index');
            $table->index('is_read', 'notification_is_read_index');
            $table->index('created_at', 'notification_created_at_index');

            $table->foreign('user_id')->references('id')->on('user')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropForeignIfExists('notification', 'notification_user_id_foreign');
        Schema::dropIfExists('notification');
    }
}
