<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateAcademyPaymentReminderTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_payment_reminder', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('instalment_id')->index();
            $table->dateTime('sent_at')->nullable();
            $table->string('type')->default('due_soon'); // due_soon, overdue, failed
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_payment_reminder');
    }
}
