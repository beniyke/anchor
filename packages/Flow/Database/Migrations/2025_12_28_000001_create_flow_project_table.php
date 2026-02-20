<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateFlowProjectTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('flow_project', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('owner_id')->index();
            $table->dateTimestamps();
            $table->softDeletes();

            $table->index('refid');
            $table->foreign('owner_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flow_project');
    }
}
