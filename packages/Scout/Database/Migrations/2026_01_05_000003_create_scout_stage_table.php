<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_05_000003_create_scout_stage_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateScoutStageTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('scout_stage', function (SchemaBuilder $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scout_stage');
    }
}
