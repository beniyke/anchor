<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the wave_invoice table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateWaveInvoiceTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExist('wave_invoice', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid')->unique()->index();
            $table->unsignedBigInteger('owner_id')->index();
            $table->string('owner_type', 50)->index();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->string('status', 32)->index();
            $table->bigInteger('amount');
            $table->bigInteger('tax')->default(0);
            $table->bigInteger('total');
            $table->string('currency', 3)->default('USD');
            $table->string('invoice_number')->unique()->index();
            $table->dateTime('due_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTimestamps();

            $table->foreign('subscription_id')->references('id')->on('wave_subscription')->onDelete('SET NULL');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wave_invoice');
    }
}
