<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create hub_mention table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateHubMentionTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('hub_mention', function ($table) {
            $table->id();
            $table->unsignedBigInteger('message_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->datetime('notified_at')->nullable();

            $table->foreign('message_id')
                ->references('id')
                ->on('hub_message')
                ->onDelete('cascade');

            $table->unique(['message_id', 'user_id'], 'hub_mention_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hub_mention');
    }
}
