<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_07_000010_create_metric_recognition_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateMetricRecognitionTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('metric_recognition', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id')->index();
            $table->unsignedBigInteger('receiver_id')->index();
            $table->string('award_type'); // kudos, excellence, etc.
            $table->text('message');
            $table->dateTimestamps();

            $table->foreign('sender_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metric_recognition');
    }
}
