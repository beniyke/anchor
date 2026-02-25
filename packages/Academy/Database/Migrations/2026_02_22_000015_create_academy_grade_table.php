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

class CreateAcademyGradeTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_grade', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('submission_id')->index()->unique();
            $table->unsignedBigInteger('graded_by')->nullable()->index(); // User ID of instructor
            $table->integer('raw_score')->default(0);
            $table->integer('percent_score')->default(0);
            $table->boolean('is_passing')->default(false);
            $table->dateTime('graded_at')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_grade');
    }
}
