<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_04_000002_create_proof_testimonial_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateProofTestimonialTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->create('proof_testimonial', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('proof_source_id');
            $table->text('content');
            $table->integer('rating')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->string('video_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->dateTimestamps();

            $table->foreign('proof_source_id')
                ->references('id')
                ->on('proof_source')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('proof_testimonial');
    }
}
