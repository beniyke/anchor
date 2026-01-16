<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create support_ticket table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateSupportTicketTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('support_ticket', function ($table) {
            $table->id();
            $table->string('refid', 64)->unique();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->unsignedBigInteger('assigned_to')->nullable()->index();
            $table->string('subject', 255);
            $table->text('description');
            $table->string('status', 20)->default('open')->index();
            $table->string('priority', 20)->default('medium')->index();
            $table->datetime('resolved_at')->nullable();
            $table->datetime('closed_at')->nullable();
            $table->datetime('sla_due_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->dateTimestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('id')
                ->on('support_category')
                ->onDelete('set null');

            $table->foreign('assigned_to')
                ->references('id')
                ->on('user')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket');
    }
}
