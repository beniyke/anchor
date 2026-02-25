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

class CreateAcademyFeedbackTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_feedback', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('submission_id')->index();
            $table->unsignedBigInteger('user_id')->index(); // Who gave the feedback
            $table->unsignedBigInteger('question_id')->nullable()->index(); // Optional: specific to a question
            $table->text('comment');
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_feedback');
    }
}
