<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_04_000004_create_proof_case_section_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateProofCaseSectionTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->create('proof_case_section', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('proof_case_study_id');
            $table->string('title')->nullable();
            $table->longText('content');
            $table->integer('order')->default(0);
            $table->dateTimestamps();

            $table->foreign('proof_case_study_id')
                ->references('id')
                ->on('proof_case_study')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('proof_case_section');
    }
}
