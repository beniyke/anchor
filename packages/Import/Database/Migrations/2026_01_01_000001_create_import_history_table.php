<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create import_history table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateImportHistoryTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('import_history', function ($table) {
            $table->id();
            $table->string('refid', 64)->unique();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('importer_class', 255);
            $table->string('filename', 255);
            $table->string('original_filename', 255)->nullable();
            $table->string('disk', 50)->default('local');
            $table->string('path', 500)->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->text('error')->nullable();
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('processed_rows')->default(0);
            $table->unsignedInteger('success_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);
            $table->unsignedInteger('skipped_rows')->default(0);
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
        Schema::dropIfExists('import_history');
    }
}
