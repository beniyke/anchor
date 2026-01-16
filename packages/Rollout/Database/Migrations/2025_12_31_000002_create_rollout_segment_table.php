<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create rollout_segment table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateRolloutSegmentTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('rollout_segment', function ($table) {
            $table->id();
            $table->unsignedBigInteger('feature_id');
            $table->string('name', 100);
            $table->string('type', 50);
            $table->string('value', 255)->nullable();
            $table->dateTimestamps();

            $table->foreign('feature_id')
                ->references('id')
                ->on('rollout_feature')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rollout_segment');
    }
}
