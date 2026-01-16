<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_05_000009_create_scout_candidate_file_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateScoutCandidateFileTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('scout_candidate_file', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('scout_candidate_id')->index();
            $table->string('type')->default('resume'); // resume, cover_letter, portfolio, other
            $table->unsignedBigInteger('media_id')->index(); // Integration with Media package
            $table->dateTimestamps();

            $table->foreign('scout_candidate_id')->references('id')->on('scout_candidate')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scout_candidate_file');
    }
}
