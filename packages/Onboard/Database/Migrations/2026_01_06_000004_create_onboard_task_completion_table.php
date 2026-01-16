<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_06_000004_create_onboard_task_completion_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateOnboardTaskCompletionTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('onboard_task_completion', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('onboard_task_id')->index();
            $table->dateTime('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->dateTimestamps();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('onboard_task_id')->references('id')->on('onboard_task')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboard_task_completion');
    }
}
