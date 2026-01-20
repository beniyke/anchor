<?php

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateActivityTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('activity')) {
            Schema::create('activity', function (SchemaBuilder $table) {
                $table->id();
                $table->bigInteger('user_id')->unsigned();
                $table->text('description');
                $table->dateTimestamps();
                $table->foreign('user_id')->references('id')->on('user')->onDelete('CASCADE');
                $table->index('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropForeignIfExists('activity', 'activity_user_id_foreign');
        Schema::dropIfExists('activity');
    }
}
