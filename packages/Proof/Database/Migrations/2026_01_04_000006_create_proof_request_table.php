<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateProofRequestTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->createIfNotExists('proof_request', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('proof_source_id');
            $table->string('token')->unique();
            $table->string('status')->default('sent'); // sent, completed, expired
            $table->dateTimestamps();

            $table->foreign('proof_source_id')
                ->references('id')
                ->on('proof_source')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('proof_request');
    }
}
