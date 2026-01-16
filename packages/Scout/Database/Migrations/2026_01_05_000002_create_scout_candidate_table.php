<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_05_000002_create_scout_candidate_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateScoutCandidateTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('scout_candidate', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('name');
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->string('status')->default('active'); // active, blacklisted, inactive
            $table->boolean('is_talent_pool')->default(false);
            $table->dateTimestamps();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scout_candidate');
    }
}
