<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create the ally_reseller table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateAllyResellerTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ally_reseller', function ($table) {
            $table->id();
            $table->string('refid', 16)->unique();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('company_name', 255)->nullable();
            $table->string('tier', 50)->default('standard');
            $table->string('status', 50)->default('active');
            $table->json('metadata')->nullable();
            $table->dateTimestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ally_reseller');
    }
}
