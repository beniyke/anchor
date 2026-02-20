<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Schema\Schema;
use Wallet\Enums\Currency;

class CreateWalletTable
{
    public function up()
    {
        Schema::createIfNotExists('wallet', function ($table) {
            $table->id();
            $table->integer('owner_id')->index();
            $table->string('owner_type', 50)->index();
            $table->string('refid')->index();
            $table->integer('balance')->default(0);
            $table->enum('currency', Currency::class)->default(Currency::USD->value)->index();
            $table->dateTime('last_transaction_at')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet');
    }
}
