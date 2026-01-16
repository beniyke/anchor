<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create stack_form table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateStackFormTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('stack_form', function ($table) {
            $table->id();
            $table->string('refid', 64)->unique();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('status', 32)->default('draft')->index(); // draft, active, archived
            $table->json('settings')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stack_form');
    }
}
