<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateBlishListSubscriberTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->createIfNotExists('blish_list_subscriber', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedInteger('subscriber_id');
            $table->unsignedInteger('list_id');

            $table->foreign('subscriber_id')->references('id')->on('blish_subscriber')->onDelete('cascade');
            $table->foreign('list_id')->references('id')->on('blish_list')->onDelete('cascade');

            $table->unique(['subscriber_id', 'list_id']);
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('blish_list_subscriber');
    }
}
