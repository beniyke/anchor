<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create hub_reminder table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateHubReminderTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('hub_reminder', function ($table) {
            $table->id();
            $table->string('refid', 64)->unique();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('remindable_type', 255)->nullable()->index();
            $table->unsignedBigInteger('remindable_id')->nullable()->index();
            $table->text('message');
            $table->datetime('remind_at')->index();
            $table->string('repeat_interval', 20)->default('none');
            $table->string('status', 20)->default('pending')->index();
            $table->datetime('completed_at')->nullable();
            $table->datetime('snoozed_until')->nullable();
            $table->unsignedInteger('snooze_count')->default(0);
            $table->json('metadata')->nullable();
            $table->dateTimestamps();

            $table->index(['remindable_type', 'remindable_id'], 'hub_reminder_remindable_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hub_reminder');
    }
}
