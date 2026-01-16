<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_05_000006_create_scout_scorecard_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateScoutScorecardTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('scout_scorecard', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('scout_interview_id')->index();
            $table->unsignedBigInteger('user_id')->index(); // Reviewer
            $table->json('ratings')->nullable(); // structured ratings
            $table->text('comments')->nullable();
            $table->string('recommendation')->nullable(); // strong_hire, hire, no_hire, strong_no_hire
            $table->dateTimestamps();

            $table->foreign('scout_interview_id')->references('id')->on('scout_interview')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scout_scorecard');
    }
}
