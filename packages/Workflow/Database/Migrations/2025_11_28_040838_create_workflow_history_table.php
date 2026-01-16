<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the workflow history table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateWorkflowHistoryTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_history', function (SchemaBuilder $table) {
            $table->id();
            $table->string('instance_id')->index();
            $table->string('type');
            $table->text('payload')->nullable();
            $table->text('result')->nullable();
            $table->string('workflow_class')->nullable();
            $table->text('input')->nullable();
            $table->dateTimestamps();

            $table->index(['instance_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_history');
    }
}
