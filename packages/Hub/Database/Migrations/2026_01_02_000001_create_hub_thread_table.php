<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create hub_thread table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateHubThreadTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('hub_thread', function ($table) {
            $table->id();
            $table->string('refid', 64)->unique();
            $table->string('threadable_type', 255)->nullable()->index();
            $table->unsignedBigInteger('threadable_id')->nullable()->index();
            $table->string('title', 255)->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->dateTimestamps();

            $table->index(['threadable_type', 'threadable_id'], 'hub_thread_threadable_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hub_thread');
    }
}
