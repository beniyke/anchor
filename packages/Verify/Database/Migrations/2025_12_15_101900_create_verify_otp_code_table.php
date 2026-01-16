<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the verify_otp_code table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;

class CreateVerifyOtpCodeTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->schema()->create('verify_otp_code', function ($table) {
            $table->id();
            $table->string('identifier');
            $table->string('refid')->unique()->index();
            $table->string('code');
            $table->string('channel', 50);
            $table->dateTime('expires_at');
            $table->dateTime('verified_at')->nullable();
            $table->dateTimestamps();

            $table->index('identifier');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->schema()->dropIfExists('verify_otp_code');
    }
}
