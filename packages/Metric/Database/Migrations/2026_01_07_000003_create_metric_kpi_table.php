<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateMetricKpiTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('metric_kpi', function (SchemaBuilder $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('unit')->default('number');
            $table->string('frequency')->default('monthly'); // daily, weekly, monthly, quarterly
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metric_kpi');
    }
}
