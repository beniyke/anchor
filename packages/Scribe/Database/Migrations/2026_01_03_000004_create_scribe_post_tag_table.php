<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create scribe_post_tag pivot table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateScribePostTagTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('scribe_post_tag', function ($table) {
            $table->unsignedBigInteger('scribe_post_id')->index();
            $table->unsignedBigInteger('scribe_tag_id')->index();

            $table->primary(['scribe_post_id', 'scribe_tag_id']);
            $table->foreign('scribe_post_id')->references('id')->on('scribe_post')->onDelete('cascade');
            $table->foreign('scribe_tag_id')->references('id')->on('scribe_tag')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scribe_post_tag');
    }
}
