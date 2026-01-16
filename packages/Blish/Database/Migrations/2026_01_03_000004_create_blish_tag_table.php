<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_03_000004_create_blish_tag_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateBlishTagTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->create('blish_tag', function (SchemaBuilder $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('blish_tag');
    }
}
