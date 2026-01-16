<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the wave_plan table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateWavePlanTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExist('wave_plan', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid')->unique()->index();
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->text('description')->nullable();
            $table->bigInteger('price')->columnComment('Price in smallest currency unit');
            $table->string('currency', 3)->default('USD');
            $table->string('interval')->default('month');
            $table->integer('interval_count')->default(1);
            $table->integer('trial_days')->default(0);
            $table->string('status', 32)->default('active')->index();
            $table->json('metadata')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wave_plan');
    }
}
