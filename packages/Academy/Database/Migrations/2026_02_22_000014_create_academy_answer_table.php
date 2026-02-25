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

class CreateAcademyAnswerTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_answer', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('submission_id')->index();
            $table->unsignedBigInteger('question_id')->index();
            $table->unsignedBigInteger('choice_id')->nullable()->index(); // For MCQs
            $table->text('content')->nullable(); // For short/long answers
            $table->string('file_path')->nullable(); // For file uploads
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_answer');
    }
}
