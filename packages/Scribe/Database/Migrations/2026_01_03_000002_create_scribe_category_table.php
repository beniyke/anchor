<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create scribe_category table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateScribeCategoryTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('scribe_category', function ($table) {
            $table->id();
            $table->string('refid', 64)->unique()->index();
            $table->string('name', 128);
            $table->string('slug', 128)->unique()->index();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->dateTimestamps();

            $table->foreign('parent_id')->references('id')->on('scribe_category')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scribe_category');
    }
}
