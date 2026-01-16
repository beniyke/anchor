<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration for creating the slot_booking table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateSlotBookingTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('slot_booking', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid')->unique();
            $table->bigInteger('schedule_id')->unsigned();
            $table->string('bookable_type');
            $table->bigInteger('bookable_id')->unsigned();
            $table->datetime('starts_at');
            $table->datetime('ends_at');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->text('metadata')->nullable();
            $table->dateTimestamps();

            $table->foreign('schedule_id')->references('id')->on('slot_schedule')->onDelete('cascade');
            $table->index(['bookable_type', 'bookable_id'], 'slot_booking_bookable_index');
            $table->index('status');
            $table->index(['starts_at', 'ends_at'], 'slot_booking_time_range_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slot_booking');
    }
}
