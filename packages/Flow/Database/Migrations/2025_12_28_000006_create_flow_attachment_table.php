<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * 2025_12_28_000006_create_flow_attachment_table
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateFlowAttachmentTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('flow_attachment', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid')->unique();
            $table->unsignedBigInteger('task_id');
            $table->string('path');
            $table->string('filename');
            $table->string('mime_type')->nullable();
            $table->bigInteger('size')->default(0);
            $table->unsignedBigInteger('uploaded_by')->index();
            $table->dateTimestamps();

            $table->index('refid');
            $table->index(['task_id', 'uploaded_by']);

            $table->foreign('task_id')->references('id')->on('flow_task')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flow_attachment');
    }
}
