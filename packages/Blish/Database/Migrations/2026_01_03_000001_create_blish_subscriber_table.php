<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_03_000001_create_blish_subscriber_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateBlishSubscriberTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->create('blish_subscriber', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 32)->unique();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('status')->default('pending'); // pending, active, unsubscribed
            $table->datetime('verified_at')->nullable();
            $table->datetime('unsubscribed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTimestamps();

            $table->index('status');
            $table->index('email');
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('blish_subscriber');
    }
}
