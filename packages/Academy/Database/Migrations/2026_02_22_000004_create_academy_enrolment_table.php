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

class CreateAcademyEnrolmentTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('academy_enrolment', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid', 20)->unique()->nullable();
            $table->unsignedBigInteger('program_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('payment_plan_id')->nullable()->index();
            $table->string('status')->default('pending'); // pending, active, suspended, completed, expired, cancelled
            $table->string('admission_id', 30)->unique()->nullable()->index();
            $table->dateTime('enrolled_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->integer('progress_percent')->default(0);
            $table->text('metadata')->nullable();
            $table->dateTimestamps();

            $table->unique(['program_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_enrolment');
    }
}
