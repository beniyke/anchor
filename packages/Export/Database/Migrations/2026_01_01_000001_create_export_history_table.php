<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create export_history table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateExportHistoryTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('export_history', function ($table) {
            $table->id();
            $table->string('refid', 64)->unique();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('exporter_class', 255);
            $table->string('format', 20);
            $table->string('filename', 255);
            $table->string('disk', 50)->default('local');
            $table->string('path', 500)->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->text('error')->nullable();
            $table->unsignedInteger('rows_count')->default(0);
            $table->unsignedBigInteger('file_size')->default(0);
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->dateTimestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_history');
    }
}
