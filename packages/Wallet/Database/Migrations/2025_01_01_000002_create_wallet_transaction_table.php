<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * 2025_01_01_000002_create_wallet_transaction_table
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Schema\Schema;
use Wallet\Enums\TransactionStatus;
use Wallet\Enums\TransactionType;

class CreateWalletTransactionTable
{
    public function up()
    {
        Schema::create('wallet_transaction', function ($table) {
            $table->id();
            $table->integer('wallet_id')->index();
            $table->enum('type', TransactionType::class)->index();
            $table->string('refid')->index();
            $table->integer('amount');
            $table->integer('fee')->default(0);
            $table->integer('net_amount');
            $table->integer('balance_before');
            $table->integer('balance_after');
            $table->string('reference_id')->unique();
            $table->string('idempotency_key')->nullable()->unique();
            $table->integer('parent_transaction_id')->nullable()->index();
            $table->string('payment_processor', 50)->nullable();
            $table->string('processor_transaction_id')->nullable();
            $table->integer('processor_fee')->default(0);
            $table->text('description')->nullable();
            $table->text('metadata')->nullable();
            $table->enum('status', TransactionStatus::class)->default(TransactionStatus::COMPLETED->value)->index();
            $table->dateTime('completed_at')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet_transaction');
    }
}
