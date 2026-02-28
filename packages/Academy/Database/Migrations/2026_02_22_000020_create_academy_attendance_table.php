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
            $table->string('attendable_type')->index();
            $table->unsignedBigInteger('attendable_id')->index();
            $table->unsignedBigInteger('enrolment_id')->index();
            $table->dateTime('joined_at')->nullable();
            $table->dateTime('left_at')->nullable();
            $table->integer('duration')->default(0); // in minutes
            $table->dateTimestamps();

            $table->unique(['attendable_type', 'attendable_id', 'enrolment_id'], 'academy_attendance_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_attendance');
    }
}
