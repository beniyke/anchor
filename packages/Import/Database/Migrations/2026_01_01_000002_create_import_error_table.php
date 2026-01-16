<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create import_error table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\DB;
use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateImportErrorTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('import_error', function ($table) {
            $table->id();
            $table->unsignedBigInteger('import_id')->index();
            $table->unsignedInteger('row_number');
            $table->string('column', 100)->nullable();
            $table->text('value')->nullable();
            $table->text('error');
            $table->json('row_data')->nullable();
            $table->datetime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('import_id')
                ->references('id')
                ->on('import_history')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_error');
    }
}
