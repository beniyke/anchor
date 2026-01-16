<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create scribe_post table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateScribePostTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('scribe_post', function ($table) {
            $table->id();
            $table->string('refid', 64)->unique()->index();
            $table->string('title', 255);
            $table->string('slug', 255)->unique()->index();
            $table->text('content');
            $table->text('excerpt')->nullable();
            $table->string('status', 32)->default('draft')->index(); // published, draft, scheduled
            $table->dateTime('published_at')->nullable()->index();
            $table->unsignedBigInteger('scribe_category_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index(); // Author ID
            $table->json('seo_meta')->nullable();
            $table->json('settings')->nullable();
            $table->dateTimestamps();

            $table->foreign('scribe_category_id')->references('id')->on('scribe_category')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scribe_post');
    }
}
