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
        if (! Schema::hasTable('user')) {
            Schema::create('user', function (SchemaBuilder $table) {
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
                $table->unsignedBigInteger('role_id')->nullable();
                $table->dateTime('activation_token_created_at')->nullable();
                $table->dateTime('password_updated_at')->nullable();
                $table->dateTimestamps();
                $table->index('refid');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
}
