<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the watcher_entry table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateWatcherEntryTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('watcher_entry', function (SchemaBuilder $table) {
            $table->id();
            $table->string('batch_id', 36)->nullable()->index();
            $table->string('type', 50)->index();
            $table->string('family_hash', 64)->nullable()->index();
            $table->text('content');
            $table->dateTime('created_at')->index();

            // Performance indexes
            $table->index(['type', 'created_at']);
            $table->index(['batch_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watcher_entry');
    }
}
