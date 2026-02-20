<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreatePulseReportTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::createIfNotExists('pulse_report', function ($table) {
            $table->id();
            $table->string('reportable_type');
            $table->unsignedBigInteger('reportable_id');
            $table->unsignedBigInteger('user_id');
            $table->string('reason');
            $table->string('status')->default('pending');
            $table->dateTimestamps();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->index(['reportable_type', 'reportable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pulse_report');
    }
}
