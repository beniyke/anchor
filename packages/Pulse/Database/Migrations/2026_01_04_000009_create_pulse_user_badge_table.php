<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_04_000009_create_pulse_user_badge_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreatePulseUserBadgeTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pulse_user_badge', function ($table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('pulse_badge_id');
            $table->dateTimestamps();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('pulse_badge_id')->references('id')->on('pulse_badge')->onDelete('cascade');
            $table->primary(['user_id', 'pulse_badge_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pulse_user_badge');
    }
}
