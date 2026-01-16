<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the wave_discount table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateWaveDiscountTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExist('wave_discount', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid')->unique()->index();
            $table->unsignedBigInteger('owner_id')->index();
            $table->string('owner_type', 50)->index();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('coupon_id');
            $table->bigInteger('amount_saved');
            $table->dateTimestamps();

            $table->foreign('subscription_id')->references('id')->on('wave_subscription')->onDelete('CASCADE');
            $table->foreign('invoice_id')->references('id')->on('wave_invoice')->onDelete('CASCADE');
            $table->foreign('coupon_id')->references('id')->on('wave_coupon')->onDelete('RESTRICT');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wave_discount');
    }
}
