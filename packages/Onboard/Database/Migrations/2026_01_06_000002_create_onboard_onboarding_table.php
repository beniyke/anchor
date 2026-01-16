<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_06_000002_create_onboard_onboarding_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateOnboardOnboardingTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('onboard_onboarding', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique()->index();
            $table->unsignedBigInteger('onboard_template_id')->index();
            $table->string('status')->default('not_started'); // not_started, in_progress, completed, overdue
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->dateTimestamps();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('onboard_template_id')->references('id')->on('onboard_template')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboard_onboarding');
    }
}
