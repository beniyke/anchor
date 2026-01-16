<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_07_000008_create_metric_competency_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateMetricCompetencyTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('metric_competency', function (SchemaBuilder $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('category')->nullable(); // soft_skill, technical, leadership
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metric_competency');
    }
}
