<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create stack_field table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateStackFieldTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('stack_field', function ($table) {
            $table->id();
            $table->unsignedBigInteger('stack_form_id')->index();
            $table->string('name', 64);
            $table->string('label', 255);
            $table->string('type', 32); // text, email, select, file, etc.
            $table->string('placeholder', 255)->nullable();
            $table->json('field_rules')->nullable(); // validation rules
            $table->integer('order_index')->default(0);
            $table->json('settings')->nullable();
            $table->dateTimestamps();

            $table->foreign('stack_form_id')->references('id')->on('stack_form')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stack_field');
    }
}
