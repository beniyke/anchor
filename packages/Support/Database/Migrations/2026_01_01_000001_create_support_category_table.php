<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create support_category table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateSupportCategoryTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('support_category', function ($table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_category');
    }
}
