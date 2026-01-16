<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the wave_coupon table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateWaveCouponTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExist('wave_coupon', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid')->unique()->index();
            $table->string('code')->unique()->index();
            $table->string('name');
            $table->string('type', 32);
            $table->bigInteger('value');
            $table->string('currency', 3)->nullable();
            $table->string('duration', 32)->default('once');
            $table->integer('duration_in_months')->nullable();
            $table->integer('max_redemptions')->nullable();
            $table->integer('times_redeemed')->default(0);
            $table->dateTime('expires_at')->nullable();
            $table->string('status', 32)->default('active')->index();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wave_coupon');
    }
}
