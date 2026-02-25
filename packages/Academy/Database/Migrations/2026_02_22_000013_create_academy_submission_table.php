<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateAcademySubmissionTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_submission', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('assessment_id')->index();
            $table->unsignedBigInteger('enrolment_id')->index();
            $table->string('status')->default('pending'); // pending, graded, returned, late
            $table->dateTime('submitted_at')->nullable();
            $table->integer('attempt_number')->default(1);
            $table->integer('time_spent')->default(0); // in seconds
            $table->text('metadata')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_submission');
    }
}
