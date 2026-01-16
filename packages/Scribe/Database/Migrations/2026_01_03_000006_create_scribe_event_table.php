<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create scribe_event table for analytics.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateScribeEventTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('scribe_event', function ($table) {
            $table->id();
            $table->unsignedBigInteger('scribe_post_id')->nullable()->index();
            $table->string('event_type', 32)->index(); // view, share, read
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('session_id', 64)->nullable()->index();
            $table->json('data')->nullable();
            $table->dateTimestamps();

            $table->foreign('scribe_post_id')->references('id')->on('scribe_post')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scribe_event');
    }
}
