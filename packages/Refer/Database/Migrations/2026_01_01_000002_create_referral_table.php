<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create referral table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateReferralTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('referral', function ($table) {
            $table->id();
            $table->unsignedBigInteger('code_id')->index();
            $table->unsignedBigInteger('referrer_id')->index();
            $table->unsignedBigInteger('referee_id')->unique();
            $table->string('status', 20)->default('pending')->index();
            $table->unsignedInteger('referrer_reward')->default(0);
            $table->unsignedInteger('referee_reward')->default(0);
            $table->datetime('rewarded_at')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTimestamps();

            $table->foreign('code_id')
                ->references('id')
                ->on('referral_code')
                ->onDelete('cascade');

            $table->foreign('referrer_id')
                ->references('id')
                ->on('user')
                ->onDelete('cascade');

            $table->foreign('referee_id')
                ->references('id')
                ->on('user')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral');
    }
}
