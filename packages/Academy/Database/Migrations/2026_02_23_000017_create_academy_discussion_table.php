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

class CreateAcademyDiscussionTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_discussion', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('program_id')->index();
            $table->unsignedBigInteger('lesson_id')->nullable()->index();
            $table->unsignedBigInteger('parent_id')->nullable()->index(); // For replies
            $table->string('type')->default('general'); // general, lesson, program, announcement
            $table->text('content');
            $table->boolean('is_resolved')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->text('metadata')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_discussion');
    }
}
