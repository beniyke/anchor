<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the verify_attempt table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;

class CreateVerifyAttemptTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->schema()->create('verify_attempt', function ($table) {
            $table->id();
            $table->string('identifier');
            $table->string('refid')->unique()->index();
            $table->enum('attempt_type', ['generation', 'verification']);
            $table->integer('count')->unsigned()->default(0);
            $table->dateTime('window_start');
            $table->dateTimestamps();

            $table->unique(['identifier', 'attempt_type']);
            $table->index('window_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->schema()->dropIfExists('verify_attempt');
    }
}
