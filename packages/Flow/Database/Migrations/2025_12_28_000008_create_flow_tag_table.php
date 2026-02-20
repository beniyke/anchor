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

class CreateFlowTagTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('flow_tag', function (SchemaBuilder $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flow_tag');
    }
}
