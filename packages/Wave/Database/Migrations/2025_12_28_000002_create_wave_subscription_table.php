<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the wave_subscription table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateWaveSubscriptionTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExist('wave_subscription', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid')->unique()->index();
            $table->unsignedBigInteger('owner_id')->index();
            $table->string('owner_type', 50)->index();
            $table->unsignedBigInteger('plan_id');
            $table->string('status', 32)->index();
            $table->integer('quantity')->default(1);
            $table->dateTime('trial_ends_at')->nullable();
            $table->dateTime('current_period_start')->nullable();
            $table->dateTime('current_period_end')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTimestamps();

            $table->indexIfNotExist(['owner_type', 'owner_id'], 'idx_subscription_owner');
            $table->foreign('plan_id')->references('id')->on('wave_plan')->onDelete('RESTRICT');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wave_subscription');
    }
}
