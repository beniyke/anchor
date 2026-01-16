<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_07_000004_create_metric_kpi_value_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateMetricKpiValueTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('metric_kpi_value', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('metric_kpi_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index(); // Can be global or per user
            $table->decimal('value', 15, 2);
            $table->dateTime('measured_at');
            $table->json('meta')->nullable();
            $table->dateTimestamps();

            $table->foreign('metric_kpi_id')->references('id')->on('metric_kpi')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metric_kpi_value');
    }
}
