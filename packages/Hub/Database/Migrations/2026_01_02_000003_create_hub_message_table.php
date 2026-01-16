<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create hub_message table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateHubMessageTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('hub_message', function ($table) {
            $table->id();
            $table->string('refid', 64)->unique();
            $table->unsignedBigInteger('thread_id')->index();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->text('body');
            $table->boolean('is_pinned')->default(false);
            $table->json('metadata')->nullable();
            $table->dateTimestamps();

            $table->foreign('thread_id')
                ->references('id')
                ->on('hub_thread')
                ->onDelete('cascade');

            $table->foreign('parent_id')
                ->references('id')
                ->on('hub_message')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hub_message');
    }
}
