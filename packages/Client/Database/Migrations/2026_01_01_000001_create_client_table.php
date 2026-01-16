<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create client table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateClientTable extends BaseMigration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Schema::create('client', function ($table) {
            $table->id();
            $table->string('refid', 16)->unique();
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->string('phone', 50)->nullable();
            $table->string('status', 50)->default('pending');
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable(); // Reseller
            $table->unsignedBigInteger('user_id')->nullable();  // Associated User (optional)
            $table->dateTimestamps();
            $table->softDeletes();

            $table->foreign('owner_id')
                ->references('id')
                ->on('user')
                ->onDelete('set null');

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('client');
    }
}
