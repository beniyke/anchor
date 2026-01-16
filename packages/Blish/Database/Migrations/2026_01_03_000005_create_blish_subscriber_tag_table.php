<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_03_000005_create_blish_subscriber_tag_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateBlishSubscriberTagTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->create('blish_subscriber_tag', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedInteger('subscriber_id');
            $table->unsignedInteger('tag_id');

            $table->foreign('subscriber_id')->references('id')->on('blish_subscriber')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('blish_tag')->onDelete('cascade');

            $table->unique(['subscriber_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('blish_subscriber_tag');
    }
}
