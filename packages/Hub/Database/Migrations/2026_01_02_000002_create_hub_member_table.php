<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create hub_member table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateHubMemberTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('hub_member', function ($table) {
            $table->id();
            $table->unsignedBigInteger('thread_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('role', 20)->default('member');
            $table->datetime('last_read_at')->nullable();
            $table->boolean('notifications_enabled')->default(true);
            $table->datetime('joined_at');

            $table->foreign('thread_id')
                ->references('id')
                ->on('hub_thread')
                ->onDelete('cascade');

            $table->unique(['thread_id', 'user_id'], 'hub_member_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hub_member');
    }
}
