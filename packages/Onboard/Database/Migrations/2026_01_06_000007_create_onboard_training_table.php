<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_06_000007_create_onboard_training_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateOnboardTrainingTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('onboard_training', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('onboard_template_id')->index();
            $table->string('name');
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->dateTimestamps();

            $table->foreign('onboard_template_id')->references('id')->on('onboard_template')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboard_training');
    }
}
