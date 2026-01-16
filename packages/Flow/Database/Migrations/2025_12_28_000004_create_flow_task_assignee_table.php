<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * 2025_12_28_000004_create_flow_task_assignee_table
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateFlowTaskAssigneeTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('flow_task_assignee', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');
            $table->dateTimestamps();

            $table->foreign('task_id')->references('id')->on('flow_task')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->unique(['task_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flow_task_assignee');
    }
}
