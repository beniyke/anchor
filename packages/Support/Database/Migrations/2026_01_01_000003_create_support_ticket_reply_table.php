<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create support_ticket_reply table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateSupportTicketReplyTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('support_ticket_reply', function ($table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->text('message');
            $table->boolean('is_internal')->default(false);
            $table->json('attachments')->nullable();
            $table->dateTimestamps();

            $table->foreign('ticket_id')
                ->references('id')
                ->on('support_ticket')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_reply');
    }
}
