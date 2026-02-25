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

class CreateAcademyProgramBadgeTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_program_badge', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('program_id')->index();
            $table->unsignedBigInteger('badge_id')->index();
            $table->string('trigger_type')->default('completion'); // completion, milestone, grade
            $table->text('trigger_value')->nullable(); // e.g. "90" for 90% grade
            $table->dateTimestamps();

            $table->unique(['program_id', 'badge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_program_badge');
    }
}
