<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_03_000006_create_blish_template_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateBlishTemplateTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->create('blish_template', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 32)->unique();
            $table->string('name');
            $table->text('content');
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('blish_template');
    }
}
