<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create activity table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateActivityTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('activity', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('subject_id')->index()->nullable();
            $table->string('subject_type')->index()->nullable();
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->string('session_id')->index()->nullable();
            $table->string('channel', 16)->default('web')->index();
            $table->string('tag', 32)->index()->nullable();
            $table->string('level', 16)->default('info')->index();
            $table->string('date_key', 10)->index(); // YYYY-MM-DD
            $table->dateTimestamps();

            $table->index(['user_id', 'created_at'], 'activity_user_created_index');
            $table->index('created_at', 'activity_created_at_index');

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropForeignIfExists('activity', 'activity_user_id_foreign');
        Schema::dropIfExists('activity');
    }
}
