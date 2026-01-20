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
        if (! Schema::hasTable('session')) {
            Schema::create('session', function (SchemaBuilder $table) {
                $table->id();
                $table->bigInteger('user_id')->unsigned();
                $table->string('token')->unique();
                $table->string('browser');
                $table->string('device');
                $table->string('ip');
                $table->string('os');
                $table->datetime('expire_at')->nullable();
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
        Schema::dropForeignIfExists('session', 'session_user_id_foreign');
        Schema::dropIfExists('session');
    }
}
