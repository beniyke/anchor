<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * 2025_12_28_000005_create_flow_dependency_table
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateFlowDependencyTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('flow_dependency', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('depends_on_task_id');
            $table->dateTimestamps();

            $table->foreign('task_id')->references('id')->on('flow_task')->onDelete('cascade');
            $table->foreign('depends_on_task_id')->references('id')->on('flow_task')->onDelete('cascade');
            $table->unique(['task_id', 'depends_on_task_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flow_dependency');
    }
}
