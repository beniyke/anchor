<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_03_000002_create_blish_list_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateBlishListTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->create('blish_list', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 32)->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('blish_list');
    }
}
