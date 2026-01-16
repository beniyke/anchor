<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the wave_product table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateWaveProductTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExist('wave_product', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid')->unique()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->bigInteger('price');
            $table->string('currency', 3)->default('USD');
            $table->string('status', 32)->default('active')->index();
            $table->json('metadata')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wave_product');
    }
}
