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

class CreateAcademyOfflineClassTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::createIfNotExists('academy_offline_class', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique();
            $table->unsignedBigInteger('lesson_id')->index();
            $table->string('location')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('scheduled');
            $table->dateTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academy_offline_class');
    }
}
