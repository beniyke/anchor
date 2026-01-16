<?php

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateRoleTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('role')) {
            Schema::create('role', function (SchemaBuilder $table) {
                $table->id();
                $table->string('title', 100)->unique();
                $table->string('type', 20);
                $table->enum('access', ['full', 'read'])->default('full');
                $table->json('permission')->nullable();
                $table->string('refid')->unique();
                $table->dateTimestamps();
            });
        }

        Schema::whenDriverIsNot('sqlite', function () {
            Schema::table('user', function (SchemaBuilder $table) {
                $table->foreignIfNotExist('role_id')
                    ->references('id')
                    ->on('role')
                    ->onDelete('SET NULL');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::whenDriverIsNot('sqlite', function () {
            Schema::table('user', function (SchemaBuilder $table) {
                $table->dropForeignIfExists('user_role_id_foreign');
                $table->dropColumn('role_id');
            });
        });

        Schema::dropIfExists('role');
    }
}
