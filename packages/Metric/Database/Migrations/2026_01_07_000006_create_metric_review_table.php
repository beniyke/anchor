<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_07_000006_create_metric_review_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateMetricReviewTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('metric_review', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('metric_review_cycle_id')->index();
            $table->unsignedBigInteger('user_id')->index(); // Employee
            $table->unsignedBigInteger('reviewer_id')->nullable()->index(); // Manager
            $table->decimal('rating', 3, 2)->nullable();
            $table->text('summary')->nullable();
            $table->string('status')->default('pending'); // pending, self_submitted, manager_reviewed, finalized
            $table->dateTimestamps();

            $table->foreign('metric_review_cycle_id')->references('id')->on('metric_review_cycle')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('user')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metric_review');
    }
}
