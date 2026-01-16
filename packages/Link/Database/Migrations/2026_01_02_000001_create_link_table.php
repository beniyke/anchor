<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Migration to create link table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;

class CreateLinkTable extends BaseMigration
{
    public function up(): void
    {
        Schema::create('link', function ($table) {
            $table->id();
            $table->string('refid', 64)->unique();
            $table->string('token', 128)->unique()->index();
            $table->string('linkable_type', 255)->nullable()->index();
            $table->unsignedBigInteger('linkable_id')->nullable()->index();
            $table->json('scopes')->nullable();
            $table->string('recipient_type', 32)->nullable();
            $table->string('recipient_value', 255)->nullable()->index();
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('use_count')->default(0);
            $table->datetime('expires_at')->nullable()->index();
            $table->datetime('revoked_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->dateTimestamps();

            $table->index(['linkable_type', 'linkable_id'], 'link_linkable_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('link');
    }
}
