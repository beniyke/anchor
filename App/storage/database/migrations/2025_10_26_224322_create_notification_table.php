<?php

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
        if (! Schema::hasTable('notification')) {
            Schema::create('notification', function (SchemaBuilder $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->text('message');
                $table->string('url')->nullable();
                $table->string('label', 50)->nullable();
                $table->boolean('is_read')->default(0);
                $table->dateTimestamps();
                $table->index('user_id');
                $table->foreign('user_id')->references('id')->on('user')->onDelete('CASCADE');
            });
        }
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
