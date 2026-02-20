<?php

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateSessionTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::createIfNotExists('session', function (SchemaBuilder $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('token')->unique();
            $table->string('browser');
            $table->string('device');
            $table->string('ip');
            $table->string('os');
            $table->datetime('expire_at')->nullable();
            $table->dateTimestamps();

            // Performance Indexes
            $table->index('expire_at', 'session_expire_at_index');
            $table->index(['user_id', 'expire_at'], 'session_user_expire_index');
            $table->index('created_at', 'session_created_at_index');
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropForeignIfExists('session', 'session_user_id_foreign');
        Schema::dropIfExists('session');
    }
}
