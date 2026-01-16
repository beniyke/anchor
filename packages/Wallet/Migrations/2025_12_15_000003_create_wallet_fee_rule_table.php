<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the wallet_fee_rule table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateWalletFeeRuleTable extends BaseMigration
{
    public function up(): void
    {
        if (! Schema::hasTable('wallet_fee_rule')) {
            Schema::create('wallet_fee_rule', function (SchemaBuilder $table) {
                $table->id();
                $table->string('name', 100);
                $table->enum('transaction_type', ['CREDIT', 'DEBIT', 'TRANSFER']);
                $table->string('payment_processor', 50)->nullable()->columnComment('NULL means applies to all');

                $table->enum('fee_type', ['FIXED', 'PERCENTAGE', 'TIERED']);
                $table->bigInteger('fixed_amount')->default(0)->columnComment('Fixed fee in smallest unit');
                $table->decimal('percentage', 5, 4)->default(0)->columnComment('e.g., 0.0290 for 2.9%');
                $table->bigInteger('min_fee')->default(0);
                $table->bigInteger('max_fee')->nullable();

                $table->bigInteger('min_transaction_amount')->nullable();
                $table->bigInteger('max_transaction_amount')->nullable();
                $table->string('currency', 3)->default('USD');

                $table->boolean('is_active')->default(true);
                $table->dateTimestamps();

                $table->indexIfNotExist(['transaction_type', 'payment_processor'], 'idx_type_processor');
                $table->indexIfNotExist('currency', 'idx_currency');
                $table->indexIfNotExist('is_active', 'idx_active');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_fee_rule');
    }
}
