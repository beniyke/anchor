<?php

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateUserTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::createIfNotExists('user', function (SchemaBuilder $table) {
            $table->id();
            $table->string('name');
            $table->string('gender', 10);
            $table->string('phone', 20)->nullable();
            $table->string('photo')->nullable();
            $table->string('email')->unique();
            $table->string('refid')->unique();
            $table->string('password');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('inactive');
            $table->string('reset_token')->nullable();
            $table->dateTime('reset_token_created_at')->nullable();
            $table->string('activation_token')->nullable();
            $table->dateTime('activation_token_created_at')->nullable();
            $table->dateTime('password_updated_at')->nullable();
            $table->dateTimestamps();

            // Performance Indexes
            $table->index('status', 'user_status_index');
            $table->index('activation_token', 'user_activation_token_index');
            $table->index('reset_token', 'user_reset_token_index');
            $table->index(['status', 'created_at'], 'user_status_created_at_index');
            $table->index('created_at', 'user_created_at_index');
            $table->index('refid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
}
