<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_07_000001_create_metric_goal_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateMetricGoalTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('metric_goal', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('objective'); // objective, personal
            $table->string('priority')->default('medium'); // low, medium, high, critical
            $table->string('status')->default('active'); // active, completed, cancelled, overdue
            $table->decimal('progress', 5, 2)->default(0.00);
            $table->dateTime('due_at')->nullable();
            $table->dateTimestamps();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metric_goal');
    }
}
