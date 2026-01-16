<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create scribe_tag table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateScribeTagTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('scribe_tag', function ($table) {
            $table->id();
            $table->string('refid', 64)->unique()->index();
            $table->string('name', 64)->unique()->index();
            $table->string('slug', 64)->unique()->index();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scribe_tag');
    }
}
