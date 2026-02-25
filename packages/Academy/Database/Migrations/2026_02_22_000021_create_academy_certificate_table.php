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

class CreateAcademyCertificateTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_certificate', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('enrolment_id')->index()->unique();
            $table->string('certificate_number')->unique();
            $table->string('file_path')->nullable(); // Generated PDF path
            $table->dateTime('issued_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->string('status')->default('issued'); // issued, revoked, expired
            $table->text('metadata')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_certificate');
    }
}
