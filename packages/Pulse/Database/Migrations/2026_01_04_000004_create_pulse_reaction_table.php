<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_04_000004_create_pulse_reaction_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreatePulseReactionTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pulse_reaction', function ($table) {
            $table->id();
            $table->unsignedBigInteger('pulse_post_id');
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->dateTimestamps();

            $table->foreign('pulse_post_id')->references('id')->on('pulse_post')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pulse_reaction');
    }
}
