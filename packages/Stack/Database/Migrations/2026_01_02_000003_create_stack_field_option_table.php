<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create stack_field_option table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateStackFieldOptionTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('stack_field_option', function ($table) {
            $table->id();
            $table->unsignedBigInteger('stack_field_id')->index();
            $table->string('label', 255);
            $table->string('value', 255);
            $table->integer('order_index')->default(0);
            $table->dateTimestamps();

            $table->foreign('stack_field_id')->references('id')->on('stack_field')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stack_field_option');
    }
}
