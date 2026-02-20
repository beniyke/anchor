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

class CreateOnboardTemplateTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('onboard_template', function (SchemaBuilder $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboard_template');
    }
}
