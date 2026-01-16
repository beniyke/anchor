<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_05_000001_create_scout_job_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateScoutJobTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('scout_job', function (SchemaBuilder $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->string('location')->nullable();
            $table->string('type')->default('full-time'); // full-time, part-time, contract, etc.
            $table->string('salary_range')->nullable();
            $table->string('status')->default('draft'); // draft, published, closed, archived
            $table->dateTime('expires_at')->nullable();
            $table->json('meta')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scout_job');
    }
}
