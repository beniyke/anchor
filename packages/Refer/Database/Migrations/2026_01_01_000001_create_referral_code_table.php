<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create referral_code table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateReferralCodeTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('referral_code', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('code', 50)->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('uses_count')->default(0);
            $table->unsignedInteger('max_uses')->default(0);
            $table->datetime('expires_at')->nullable();
            $table->dateTimestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_code');
    }
}
