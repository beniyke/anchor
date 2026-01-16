<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_06_000006_create_onboard_document_upload_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateOnboardDocumentUploadTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('onboard_document_upload', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('onboard_document_id')->index();
            $table->unsignedBigInteger('media_id')->index(); // Integration with Media package
            $table->string('status')->default('pending'); // pending, verified, rejected
            $table->text('rejection_reason')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable()->index();
            $table->dateTimestamps();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('onboard_document_id')->references('id')->on('onboard_document')->onDelete('cascade');
            $table->foreign('verified_by')->references('id')->on('user')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboard_document_upload');
    }
}
