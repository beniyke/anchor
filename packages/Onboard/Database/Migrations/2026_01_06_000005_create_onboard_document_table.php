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

class CreateOnboardDocumentTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('onboard_document', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('onboard_template_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(true);
            $table->dateTimestamps();

            $table->foreign('onboard_template_id')->references('id')->on('onboard_template')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboard_document');
    }
}
