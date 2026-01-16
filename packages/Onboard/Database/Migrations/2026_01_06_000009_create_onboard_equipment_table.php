<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_06_000009_create_onboard_equipment_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateOnboardEquipmentTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('onboard_equipment', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('request_type'); // Laptop, Phone, Access Badge, etc.
            $table->string('status')->default('pending'); // pending, assigned, delivered
            $table->string('asset_tag')->nullable();
            $table->text('notes')->nullable();
            $table->dateTimestamps();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboard_equipment');
    }
}
