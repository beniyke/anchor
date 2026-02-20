<?php

declare(strict_types=1);

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateMetricUserManagerTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('metric_user_manager', function (SchemaBuilder $table) {
            $table->unsignedBigInteger('user_id')->unique(); // One user has one primary manager for metrics
            $table->unsignedBigInteger('manager_id')->index();
            $table->dateTimestamps();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('manager_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metric_user_manager');
    }
}
