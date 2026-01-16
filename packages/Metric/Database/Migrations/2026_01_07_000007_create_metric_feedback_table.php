<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_07_000007_create_metric_feedback_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateMetricFeedbackTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('metric_feedback', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('metric_review_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->index(); // Feedback recipient
            $table->unsignedBigInteger('author_id')->nullable()->index(); // Feedback provider
            $table->text('content');
            $table->string('type')->default('peer'); // peer, manager, self
            $table->boolean('is_anonymous')->default(false);
            $table->dateTimestamps();

            $table->foreign('metric_review_id')->references('id')->on('metric_review')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('user')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metric_feedback');
    }
}
