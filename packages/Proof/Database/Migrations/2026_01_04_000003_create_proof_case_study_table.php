<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_04_000003_create_proof_case_study_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateProofCaseStudyTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->create('proof_case_study', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('proof_source_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->string('status')->default('draft'); // draft, published
            $table->string('featured_image')->nullable();
            $table->dateTimestamps();

            $table->foreign('proof_source_id')
                ->references('id')
                ->on('proof_source')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('proof_case_study');
    }
}
