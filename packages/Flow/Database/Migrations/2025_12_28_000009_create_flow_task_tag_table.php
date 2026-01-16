<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * 2025_12_28_000009_create_flow_task_tag_table
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateFlowTaskTagTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('flow_task_tag', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('tag_id');
            $table->dateTimestamps();

            $table->foreign('task_id')->references('id')->on('flow_task')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('flow_tag')->onDelete('cascade');
            $table->unique(['task_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flow_task_tag');
    }
}
