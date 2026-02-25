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

class CreateAcademyLiveSessionTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_live_session', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('lesson_id')->index(); // Linked to a lesson of type 'live'
            $table->string('provider')->default('zoom'); // zoom, meet, teams, custom
            $table->string('meeting_id')->nullable();
            $table->string('meeting_url')->nullable();
            $table->string('meeting_password')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, live, ended, cancelled
            $table->text('recording_url')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_live_session');
    }
}
