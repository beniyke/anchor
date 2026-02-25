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

class CreateAcademyProgressTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_progress', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('enrolment_id')->index();
            $table->unsignedBigInteger('lesson_id')->index();
            $table->dateTime('completed_at')->nullable();
            $table->integer('time_spent')->default(0); // in seconds
            $table->text('metadata')->nullable();
            $table->dateTimestamps();

            $table->unique(['enrolment_id', 'lesson_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_progress');
    }
}
