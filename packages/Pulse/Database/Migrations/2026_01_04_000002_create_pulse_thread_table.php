<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_04_000002_create_pulse_thread_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreatePulseThreadTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pulse_thread', function ($table) {
            $table->id();
            $table->unsignedBigInteger('pulse_channel_id');
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->integer('view_count')->default(0);
            $table->dateTime('last_activity_at')->nullable();
            $table->dateTimestamps();

            $table->foreign('pulse_channel_id')->references('id')->on('pulse_channel')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pulse_thread');
    }
}
