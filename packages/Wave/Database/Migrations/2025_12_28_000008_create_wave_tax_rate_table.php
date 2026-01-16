<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the wave_tax_rate table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateWaveTaxRateTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExist('wave_tax_rate', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid')->unique()->index();
            $table->string('name');
            $table->decimal('rate', 5, 2);
            $table->string('country', 2)->index();
            $table->string('state', 100)->nullable()->index();
            $table->boolean('is_inclusive')->default(false);
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wave_tax_rate');
    }
}
