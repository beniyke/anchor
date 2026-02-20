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

class CreateMetricReviewCycleTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('metric_review_cycle', function (SchemaBuilder $table) {
            $table->id();
            $table->string('name');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('status')->default('draft'); // draft, active, archived
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metric_review_cycle');
    }
}
