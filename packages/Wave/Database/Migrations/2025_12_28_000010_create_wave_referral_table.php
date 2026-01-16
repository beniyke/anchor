<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the wave_referral table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateWaveReferralTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExist('wave_referral', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid')->unique()->index();
            $table->unsignedBigInteger('affiliate_id')->index();
            $table->unsignedBigInteger('referred_owner_id')->index();
            $table->string('referred_owner_type', 50)->index();
            $table->bigInteger('commission_amount');
            $table->string('status', 32)->default('pending')->index();
            $table->dateTimestamps();

            $table->foreign('affiliate_id')->references('id')->on('wave_affiliate')->onDelete('RESTRICT');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wave_referral');
    }
}
