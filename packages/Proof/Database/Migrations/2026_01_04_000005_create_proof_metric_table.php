<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateProofMetricTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->createIfNotExists('proof_metric', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('proof_case_study_id');
            $table->string('label');
            $table->string('value');
            $table->string('prefix')->nullable();
            $table->string('suffix')->nullable();
            $table->dateTimestamps();

            $table->foreign('proof_case_study_id')
                ->references('id')
                ->on('proof_case_study')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('proof_metric');
    }
}
