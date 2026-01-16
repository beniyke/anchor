<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating personal_access_token table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreatePersonalAccessTokenTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personal_access_token', function (SchemaBuilder $table) {
            $table->id();
            $table->string('tokenable_type');
            $table->bigInteger('tokenable_id')->unsigned();
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities');
            $table->dateTime('expires_at')->nullable();
            $table->dateTimestamps();

            $table->index(['tokenable_type', 'tokenable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_token');
    }
}
