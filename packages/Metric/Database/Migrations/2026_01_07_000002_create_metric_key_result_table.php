<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_07_000002_create_metric_key_result_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateMetricKeyResultTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('metric_key_result', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('metric_goal_id')->index();
            $table->string('title');
            $table->decimal('target_value', 15, 2);
            $table->decimal('current_value', 15, 2)->default(0.00);
            $table->string('unit')->default('percentage'); // percentage, currency, count
            $table->dateTimestamps();

            $table->foreign('metric_goal_id')->references('id')->on('metric_goal')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metric_key_result');
    }
}
