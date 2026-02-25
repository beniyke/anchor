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

class CreateAcademyAttendanceTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_attendance', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('live_session_id')->index();
            $table->unsignedBigInteger('enrolment_id')->index();
            $table->dateTime('joined_at')->nullable();
            $table->dateTime('left_at')->nullable();
            $table->integer('duration')->default(0); // in minutes
            $table->dateTimestamps();

            $table->unique(['live_session_id', 'enrolment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_attendance');
    }
}
