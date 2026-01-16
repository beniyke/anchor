<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * 2025_12_30_000000_create_tenant_table
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateTenantTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenant', function (SchemaBuilder $table) {
            $table->id();
            $table->string('name');
            $table->string('subdomain')->unique();
            $table->string('status')->default('active');
            $table->string('db_host')->nullable();
            $table->string('db_port')->nullable();
            $table->string('db_name')->nullable();
            $table->string('db_user')->nullable();
            $table->string('db_password')->nullable();
            $table->string('plan')->default('free');
            $table->integer('max_users')->default(10);
            $table->integer('max_storage_mb')->default(1000);
            $table->text('config')->nullable();
            $table->boolean('is_default')->default(false);
            $table->datetime('trial_ends_at')->nullable();
            $table->datetime('last_activity_at')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->dateTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant');
    }
}
