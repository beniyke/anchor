<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create stack_event table for analytics.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateStackEventTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('stack_event', function ($table) {
            $table->id();
            $table->unsignedBigInteger('stack_form_id')->index();
            $table->string('event_type', 32)->index(); // view, start, submit, error
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('session_id', 64)->nullable()->index();
            $table->json('data')->nullable();
            $table->dateTimestamps();

            $table->foreign('stack_form_id')->references('id')->on('stack_form')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stack_event');
    }
}
