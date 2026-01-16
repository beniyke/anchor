<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_03_000008_create_blish_event_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateBlishEventTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->create('blish_event', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedInteger('campaign_id');
            $table->unsignedInteger('subscriber_id');
            $table->string('type'); // delivered, open, click, bounce, complaint, unsubscribe
            $table->json('metadata')->nullable();
            $table->dateTimestamps();

            $table->foreign('campaign_id')->references('id')->on('blish_campaign')->onDelete('cascade');
            $table->foreign('subscriber_id')->references('id')->on('blish_subscriber')->onDelete('cascade');
            $table->index(['campaign_id', 'type']);
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('blish_event');
    }
}
