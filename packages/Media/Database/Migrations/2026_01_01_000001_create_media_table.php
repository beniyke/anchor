<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create media table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateMediaTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('media', function ($table) {
            $table->id();
            $table->string('uuid', 64)->unique();
            $table->string('disk', 50)->default('local');
            $table->string('path', 500);
            $table->string('filename', 255);
            $table->string('original_filename', 255);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->string('mediable_type', 255)->nullable()->index();
            $table->unsignedBigInteger('mediable_id')->nullable()->index();
            $table->string('collection', 100)->default('default')->index();
            $table->json('conversions')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
}
