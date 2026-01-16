<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_03_000009_create_blish_stat_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateBlishStatTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->create('blish_stat', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedInteger('campaign_id')->unique();
            $table->integer('recipients')->default(0);
            $table->integer('delivered')->default(0);
            $table->integer('opens')->default(0);
            $table->integer('unique_opens')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('unique_clicks')->default(0);
            $table->integer('unsubscribes')->default(0);
            $table->integer('bounces')->default(0);
            $table->dateTimestamps();

            $table->foreign('campaign_id')->references('id')->on('blish_campaign')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('blish_stat');
    }
}
