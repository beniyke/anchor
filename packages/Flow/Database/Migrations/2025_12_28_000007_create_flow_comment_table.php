<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * 2025_12_28_000007_create_flow_comment_table
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateFlowCommentTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('flow_comment', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid')->unique();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id')->index();
            $table->text('content');
            $table->json('mentions')->nullable();
            $table->dateTimestamps();

            $table->index('refid');
            $table->index(['task_id', 'user_id']);

            $table->foreign('task_id')->references('id')->on('flow_task')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flow_comment');
    }
}
