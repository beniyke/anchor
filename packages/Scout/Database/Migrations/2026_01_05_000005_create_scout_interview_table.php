<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_05_000005_create_scout_interview_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateScoutInterviewTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('scout_interview', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('scout_application_id')->index();
            $table->unsignedBigInteger('user_id')->index(); // Interviewer
            $table->unsignedBigInteger('slot_id')->nullable()->index(); // Integration with Slot package
            $table->dateTime('scheduled_at');
            $table->integer('duration')->default(30); // in minutes
            $table->string('location')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled
            $table->dateTimestamps();

            $table->foreign('scout_application_id')->references('id')->on('scout_application')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scout_interview');
    }
}
