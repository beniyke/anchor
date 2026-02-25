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

class CreateAcademyBadgeAwardTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_badge_award', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('badge_id')->index();
            $table->unsignedBigInteger('program_id')->nullable()->index();
            $table->dateTimestamps();

            $table->unique(['user_id', 'badge_id', 'program_id'], 'unique_user_badge_award');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_badge_award');
    }
}
