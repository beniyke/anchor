<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create rollout_segment_user pivot table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateRolloutSegmentUserTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('rollout_segment_user', function ($table) {
            $table->unsignedBigInteger('segment_id');
            $table->unsignedBigInteger('user_id');

            $table->primary(['segment_id', 'user_id']);

            $table->foreign('segment_id')
                ->references('id')
                ->on('rollout_segment')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rollout_segment_user');
    }
}
