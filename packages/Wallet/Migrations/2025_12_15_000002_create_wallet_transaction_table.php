<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the wallet_transaction table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\DB;
use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateWalletTransactionTable extends BaseMigration
{
    public function up(): void
    {
        if (! Schema::hasTable('wallet_transaction')) {
            Schema::create('wallet_transaction', function (SchemaBuilder $table) {
                $table->id();
                $table->unsignedBigInteger('wallet_id');
                $table->enum('type', ['CREDIT', 'DEBIT', 'REFUND', 'TRANSFER_IN', 'TRANSFER_OUT']);
                $table->string('refid')->unique()->index();

                $table->bigInteger('amount')->columnComment('Gross amount in smallest unit');
                $table->bigInteger('fee')->default(0)->columnComment('Transaction fee in smallest unit');
                $table->bigInteger('net_amount')->columnComment('Net amount (amount - fee)');

                $table->bigInteger('balance_before')->columnComment('Balance snapshot before transaction');
                $table->bigInteger('balance_after')->columnComment('Balance snapshot after transaction');

                $table->string('reference_id', 255)->columnComment('Unique reference for idempotency');
                $table->string('idempotency_key', 255)->nullable()->columnComment('Additional idempotency check');
                $table->unsignedBigInteger('parent_transaction_id')->nullable()->columnComment('For refunds/reversals');

                $table->string('payment_processor', 50)->nullable()->columnComment('paystack, stripe, etc');
                $table->string('processor_transaction_id', 255)->nullable();
                $table->bigInteger('processor_fee')->default(0)->columnComment('Fee charged by processor');

                $table->text('description')->nullable();
                $table->json('metadata')->nullable();

                $table->enum('status', ['PENDING', 'COMPLETED', 'FAILED', 'REVERSED'])->default('COMPLETED');
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('completed_at')->nullable();

                $table->indexIfNotExist(['wallet_id', 'created_at'], 'idx_wallet_created');
                $table->indexIfNotExist('reference_id', 'idx_reference');
                $table->indexIfNotExist('idempotency_key', 'idx_idempotency');
                $table->indexIfNotExist(['status', 'created_at'], 'idx_status');
                $table->indexIfNotExist('parent_transaction_id', 'idx_parent');

                $table->uniqueIfNotExist('reference_id', 'unique_reference');
                $table->uniqueIfNotExist('idempotency_key', 'unique_idempotency');

                $table->foreign('wallet_id')->references('id')->on('wallet')->onDelete('RESTRICT');
                $table->foreign('parent_transaction_id')->references('id')->on('wallet_transaction')->onDelete('SET NULL');
            });
        }
    }

    public function down(): void
    {
        Schema::dropForeignIfExists('wallet_transaction', 'wallet_transaction_wallet_id_foreign');
        Schema::dropForeignIfExists('wallet_transaction', 'wallet_transaction_parent_transaction_id_foreign');
        Schema::dropIfExists('wallet_transaction');
    }
}
