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

class CreateAcademyInstalmentTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_instalment', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('enrolment_id')->index();
            $table->integer('amount')->default(0);
            $table->integer('sequence')->default(1); // 1st, 2nd, etc.
            $table->dateTime('due_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->string('payment_reference')->nullable()->index(); // Ref to Pay package
            $table->string('status')->default('pending'); // pending, paid, partial, overdue, failed
            $table->dateTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_instalment');
    }
}
