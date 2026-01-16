<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_05_000007_create_scout_offer_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateScoutOfferTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('scout_offer', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('scout_application_id')->index();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->text('terms')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->string('status')->default('pending'); // pending, accepted, declined, expired
            $table->unsignedBigInteger('link_id')->nullable()->index(); // Integration with Link package
            $table->dateTimestamps();

            $table->foreign('scout_application_id')->references('id')->on('scout_application')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scout_offer');
    }
}
