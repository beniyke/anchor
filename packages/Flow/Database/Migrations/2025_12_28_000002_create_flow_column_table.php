<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * 2025_12_28_000002_create_flow_column_table
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateFlowColumnTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('flow_column', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('name');
            $table->string('type')->default('custom'); // todo, doing, done, custom
            $table->integer('order')->default(0);
            $table->dateTimestamps();
            $table->softDeletes();

            $table->index(['project_id', 'order']);

            $table->foreign('project_id')->references('id')->on('flow_project')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flow_column');
    }
}
