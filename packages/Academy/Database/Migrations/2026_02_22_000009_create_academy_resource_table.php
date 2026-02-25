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

class CreateAcademyResourceTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_resource', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('lesson_id')->index();
            $table->string('title');
            $table->string('type')->default('file'); // file, link, video_external
            $table->string('path')->nullable(); // Media path or URL
            $table->string('provider')->nullable(); // youtube, vimeo, bunny, etc.
            $table->integer('sort_order')->default(0);
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_resource');
    }
}
