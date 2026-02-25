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

class CreateAcademyLessonTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_lesson', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('module_id')->index();
            $table->string('title');
            $table->string('slug')->index();
            $table->string('type')->default('text'); // text, video, audio, pdf, quiz, assignment, live, etc.
            $table->text('content')->nullable();
            $table->string('duration')->nullable(); // e.g. "10:00"
            $table->integer('sort_order')->default(0);
            $table->boolean('is_preview')->default(false);
            $table->boolean('is_optional')->default(false);
            $table->text('metadata')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_lesson');
    }
}
