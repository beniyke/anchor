<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create link_usage table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateLinkUsageTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('link_usage', function ($table) {
            $table->id();
            $table->unsignedBigInteger('link_id')->index();
            $table->datetime('used_at');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->json('metadata')->nullable();

            $table->foreign('link_id')
                ->references('id')
                ->on('link')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('link_usage');
    }
}
