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

class CreateAcademyProgramMemberTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_program_member', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('program_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('role')->default('instructor'); // instructor, assistant, admin
            $table->dateTimestamps();

            $table->unique(['program_id', 'user_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_program_member');
    }
}
