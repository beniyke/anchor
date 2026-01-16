<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_04_000001_create_proof_source_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateProofSourceTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->create('proof_source', function (SchemaBuilder $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->string('job_title')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('website_url')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('proof_source');
    }
}
