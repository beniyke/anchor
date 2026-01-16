<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create scribe_comment table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateScribeCommentTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('scribe_comment', function ($table) {
            $table->id();
            $table->string('refid', 64)->unique()->index();
            $table->unsignedBigInteger('scribe_post_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->text('content');
            $table->string('status', 32)->default('pending')->index(); // approved, pending, spam
            $table->dateTimestamps();

            $table->foreign('scribe_post_id')->references('id')->on('scribe_post')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scribe_comment');
    }
}
