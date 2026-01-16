<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * 2025_01_01_000003_create_wallet_fee_rule_table
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Schema\Schema;
use Wallet\Enums\Currency;
use Wallet\Enums\FeeType;
use Wallet\Enums\TransactionType;

class CreateWalletFeeRuleTable
{
    public function up()
    {
        Schema::create('wallet_fee_rule', function ($table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('transaction_type', TransactionType::class)->index();
            $table->string('payment_processor', 50)->nullable()->index();
            $table->enum('fee_type', FeeType::class)->index();
            $table->integer('fixed_amount')->default(0);
            $table->float('percentage')->default(0);
            $table->integer('min_fee')->default(0);
            $table->integer('max_fee')->default(30000);
            $table->integer('min_transaction_amount')->nullable();
            $table->integer('max_transaction_amount')->nullable();
            $table->enum('currency', Currency::class)->default(Currency::USD->value)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->dateTimestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet_fee_rule');
    }
}
