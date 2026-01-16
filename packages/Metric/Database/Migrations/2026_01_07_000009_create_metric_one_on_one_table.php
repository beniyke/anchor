<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_07_000009_create_metric_one_on_one_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateMetricOneOnOneTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('metric_one_on_one', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('manager_id')->index();
            $table->dateTime('scheduled_at');
            $table->text('agenda')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('slot_id')->nullable()->index(); // Integration with Slot package
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled
            $table->dateTimestamps();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('manager_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metric_one_on_one');
    }
}
