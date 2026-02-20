<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateScoutApplicationTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('scout_application', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('scout_job_id')->index();
            $table->unsignedBigInteger('scout_candidate_id')->index();
            $table->unsignedBigInteger('scout_stage_id')->index();
            $table->string('status')->default('active'); // active, rejected, withdrawn, hired
            $table->text('rejection_reason')->nullable();
            $table->dateTimestamps();

            $table->foreign('scout_job_id')->references('id')->on('scout_job')->onDelete('cascade');
            $table->foreign('scout_candidate_id')->references('id')->on('scout_candidate')->onDelete('cascade');
            $table->foreign('scout_stage_id')->references('id')->on('scout_stage')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scout_application');
    }
}
