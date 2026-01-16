<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create rollout_feature table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateRolloutFeatureTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('rollout_feature', function ($table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->unsignedTinyInteger('percentage')->default(0);
            $table->datetime('starts_at')->nullable();
            $table->datetime('ends_at')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rollout_feature');
    }
}
