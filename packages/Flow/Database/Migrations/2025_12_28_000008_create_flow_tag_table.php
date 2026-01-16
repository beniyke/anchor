<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * 2025_12_28_000008_create_flow_tag_table
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateFlowTagTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('flow_tag', function (SchemaBuilder $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flow_tag');
    }
}
