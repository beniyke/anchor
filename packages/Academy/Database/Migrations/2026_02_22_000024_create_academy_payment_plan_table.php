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

class CreateAcademyPaymentPlanTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_payment_plan', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('program_id')->index();
            $table->string('name');
            $table->string('type')->default('full'); // full, instalment, free, subscription
            $table->integer('price')->default(0); // Total price in cents
            $table->string('currency')->default('USD');
            $table->integer('instalment_count')->default(1);
            $table->integer('instalment_interval')->default(0); // in days, for auto-reminders
            $table->boolean('is_active')->default(true);
            $table->text('metadata')->nullable();
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_payment_plan');
    }
}
