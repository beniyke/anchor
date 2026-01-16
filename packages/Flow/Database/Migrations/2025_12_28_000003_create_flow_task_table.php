<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * 2025_12_28_000003_create_flow_task_table
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateFlowTaskTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('flow_task', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid')->unique();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('column_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable(); // For subtasks
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('type')->default('task'); // task, bug, issue, idea
            $table->dateTime('due_date')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern')->nullable();
            $table->dateTime('next_recurrence_at')->nullable();
            $table->integer('order')->default(0);
            $table->dateTimestamps();
            $table->softDeletes();

            $table->index('refid');
            $table->index(['project_id', 'column_id', 'parent_id', 'is_recurring', 'next_recurrence_at', 'priority', 'type'], 'flow_task_composite_index');
            $table->index('parent_id');
            $table->index('is_recurring');
            $table->index('due_date');
            $table->index('priority');
            $table->index('type');

            $table->foreign('project_id')->references('id')->on('flow_project')->onDelete('cascade');
            $table->foreign('column_id')->references('id')->on('flow_column')->onDelete('set null');
            $table->foreign('parent_id')->references('id')->on('flow_task')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flow_task');
    }
}
