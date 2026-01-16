<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the wallet table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateWalletTables extends BaseMigration
{
    public function up(): void
    {
        if (! Schema::hasTable('wallet')) {
            Schema::create('wallet', function (SchemaBuilder $table) {
                $table->id();
                $table->unsignedBigInteger('owner_id');
                $table->string('owner_type', 50);
                $table->string('refid')->unique()->index();
                $table->bigInteger('balance')->default(0)->columnComment('Balance in smallest currency unit (cents)');
                $table->string('currency', 3)->default('USD')->columnComment('ISO 4217 currency code');
                $table->dateTime('last_transaction_at')->nullable();
                $table->dateTimestamps();

                $table->indexIfNotExist(['owner_type', 'owner_id'], 'idx_owner');
                $table->indexIfNotExist('currency', 'idx_currency');
                $table->indexIfNotExist('updated_at', 'idx_updated');
                $table->uniqueIfNotExist(['owner_type', 'owner_id', 'currency'], 'unique_owner');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet');
    }
}
