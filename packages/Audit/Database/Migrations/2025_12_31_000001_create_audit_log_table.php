<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create audit_log table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\DB;
use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateAuditLogTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('audit_log', function ($table) {
            $table->id();
            $table->string('refid', 64)->unique();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('event', 50)->index();
            $table->string('auditable_type', 255)->nullable()->index();
            $table->unsignedBigInteger('auditable_id')->nullable()->index();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();
            $table->string('checksum', 64)->nullable();
            $table->datetime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->index();

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
}
