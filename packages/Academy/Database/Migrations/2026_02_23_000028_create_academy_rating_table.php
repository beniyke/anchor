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

class CreateAcademyRatingTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_rating', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('program_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->integer('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->dateTimestamps();

            $table->unique(['program_id', 'user_id'], 'unique_program_user_rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_rating');
    }
}
