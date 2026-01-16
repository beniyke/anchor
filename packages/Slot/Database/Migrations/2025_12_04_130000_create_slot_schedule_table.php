<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the slot_schedule table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateSlotScheduleTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('slot_schedule', function (SchemaBuilder $table) {
            $table->id();
            $table->string('schedulable_type');
            $table->bigInteger('schedulable_id')->unsigned();
            $table->enum('type', ['availability', 'appointment', 'blocked', 'custom'])->default('availability');
            $table->string('title')->nullable();
            $table->datetime('starts_at');
            $table->datetime('ends_at');
            $table->text('recurrence_rule')->nullable();
            $table->datetime('recurrence_ends_at')->nullable();
            $table->text('metadata')->nullable();
            $table->text('overlap_rules')->nullable();
            $table->dateTimestamps();

            $table->index(['schedulable_type', 'schedulable_id'], 'slot_schedule_schedulable_index');
            $table->index('type');
            $table->index(['starts_at', 'ends_at'], 'slot_schedule_time_range_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slot_schedule');
    }
}
