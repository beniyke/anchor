<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_06_000008_create_onboard_training_progress_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateOnboardTrainingProgressTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('onboard_training_progress', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('onboard_training_id')->index();
            $table->string('status')->default('not_started'); // not_started, in_progress, completed
            $table->dateTime('completed_at')->nullable();
            $table->dateTimestamps();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('onboard_training_id')->references('id')->on('onboard_training')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboard_training_progress');
    }
}
