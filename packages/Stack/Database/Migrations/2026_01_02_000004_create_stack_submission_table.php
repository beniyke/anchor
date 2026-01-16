<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create stack_submission table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateStackSubmissionTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('stack_submission', function ($table) {
            $table->id();
            $table->string('refid', 64)->unique();
            $table->unsignedBigInteger('stack_form_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTimestamps();

            $table->foreign('stack_form_id')->references('id')->on('stack_form')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stack_submission');
    }
}
