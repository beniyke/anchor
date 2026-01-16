<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating api_key table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateApiKeyTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('api_key', function (SchemaBuilder $table) {
            $table->id();
            $table->string('name');
            $table->string('key', 64)->unique(); // Hashed key
            $table->timestamp('last_used_at')->nullable();
            $table->dateTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_key');
    }
}
