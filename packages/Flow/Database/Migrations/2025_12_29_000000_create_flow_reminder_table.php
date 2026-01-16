<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * 2025_12_29_000000_create_flow_reminder_table
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateFlowReminderTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('flow_reminder', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');
            $table->string('type'); // e.g., 'before_due'
            $table->integer('value'); // e.g., 2
            $table->string('unit'); // e.g., 'hours'
            $table->string('status')->default('active'); // active, sent, cancelled
            $table->dateTime('remind_at')->nullable();
            $table->dateTimestamps();

            $table->foreign('task_id')->references('id')->on('flow_task')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');

            $table->index(['status', 'remind_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flow_reminder');
    }
}
