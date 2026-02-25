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

class CreateAcademyProgramTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_program', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('content')->nullable(); // Extended details
            $table->string('thumbnail')->nullable();
            $table->string('banner')->nullable();
            $table->string('status')->default('draft'); // draft, published, archived
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_private')->default(false); // Link-only access
            $table->string('access_code')->nullable(); // For private programs
            $table->string('certificate_template')->nullable();
            $table->text('metadata')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_program');
    }
}
