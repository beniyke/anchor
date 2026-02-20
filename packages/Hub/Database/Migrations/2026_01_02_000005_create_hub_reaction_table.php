<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create hub_reaction table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateHubReactionTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('hub_reaction', function ($table) {
            $table->id();
            $table->unsignedBigInteger('message_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('emoji', 32);
            $table->datetime('created_at');

            $table->foreign('message_id')
                ->references('id')
                ->on('hub_message')
                ->onDelete('cascade');

            $table->unique(['message_id', 'user_id', 'emoji'], 'hub_reaction_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hub_reaction');
    }
}
