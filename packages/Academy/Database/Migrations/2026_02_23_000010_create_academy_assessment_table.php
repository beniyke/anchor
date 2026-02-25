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

class CreateAcademyAssessmentTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_assessment', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('lesson_id')->index()->unique(); // One assessment per lesson
            $table->string('type')->default('quiz'); // quiz, assignment, exam
            $table->integer('passing_score')->default(50);
            $table->integer('attempts_allowed')->default(0); // 0 = unlimited
            $table->integer('time_limit')->default(0); // in minutes, 0 = no limit
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('show_correct_answers')->default(true);
            $table->text('metadata')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_assessment');
    }
}
