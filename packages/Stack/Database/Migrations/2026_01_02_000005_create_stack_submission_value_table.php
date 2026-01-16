<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create stack_submission_value table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateStackSubmissionValueTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('stack_submission_value', function ($table) {
            $table->id();
            $table->unsignedBigInteger('stack_submission_id')->index();
            $table->unsignedBigInteger('stack_field_id')->index();
            $table->text('value')->nullable();
            $table->unsignedBigInteger('media_id')->nullable()->index(); // Link to Media package (ref only)
            $table->dateTimestamps();

            $table->foreign('stack_submission_id')->references('id')->on('stack_submission')->onDelete('cascade');
            $table->foreign('stack_field_id')->references('id')->on('stack_field')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stack_submission_value');
    }
}
