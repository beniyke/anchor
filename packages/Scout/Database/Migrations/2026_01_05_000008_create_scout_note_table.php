<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_05_000008_create_scout_note_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateScoutNoteTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('scout_note', function (SchemaBuilder $table) {
            $table->id();
            $table->string('noteable_type');
            $table->unsignedBigInteger('noteable_id');
            $table->unsignedBigInteger('user_id')->index();
            $table->text('content');
            $table->boolean('is_private')->default(false);
            $table->dateTimestamps();

            $table->index(['noteable_type', 'noteable_id']);
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scout_note');
    }
}
