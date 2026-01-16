<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_04_000003_create_pulse_post_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreatePulsePostTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pulse_post', function ($table) {
            $table->id();
            $table->unsignedBigInteger('pulse_thread_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->text('content');
            $table->dateTimestamps();

            $table->foreign('pulse_thread_id')->references('id')->on('pulse_thread')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('pulse_post')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pulse_post');
    }
}
