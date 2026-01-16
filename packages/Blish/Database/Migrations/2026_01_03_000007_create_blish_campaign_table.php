<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_03_000007_create_blish_campaign_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateBlishCampaignTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->create('blish_campaign', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 32)->unique();
            $table->string('title');
            $table->string('subject');
            $table->unsignedInteger('template_id')->nullable();
            $table->string('status')->default('draft'); // draft, scheduled, sending, sent, cancelled
            $table->datetime('scheduled_at')->nullable();
            $table->datetime('sent_at')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTimestamps();

            $table->foreign('template_id')->references('id')->on('blish_template')->onDelete('set null');
            $table->index('status');
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('blish_campaign');
    }
}
