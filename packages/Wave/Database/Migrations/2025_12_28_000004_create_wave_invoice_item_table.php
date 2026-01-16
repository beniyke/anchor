<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the wave_invoice_item table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateWaveInvoiceItemTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExist('wave_invoice_item', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid')->unique()->index();
            $table->unsignedBigInteger('invoice_id')->index();
            $table->string('description');
            $table->bigInteger('amount');
            $table->integer('quantity')->default(1);
            $table->string('type', 32)->index();
            $table->json('metadata')->nullable();
            $table->dateTimestamps();

            $table->foreign('invoice_id')->references('id')->on('wave_invoice')->onDelete('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wave_invoice_item');
    }
}
