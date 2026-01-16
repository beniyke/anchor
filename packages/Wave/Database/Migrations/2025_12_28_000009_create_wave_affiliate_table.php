<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the wave_affiliate table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateWaveAffiliateTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExist('wave_affiliate', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid')->unique()->index();
            $table->unsignedBigInteger('owner_id')->index();
            $table->string('owner_type', 50)->index();
            $table->string('code')->unique()->index();
            $table->string('status', 32)->default('active')->index();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wave_affiliate');
    }
}
