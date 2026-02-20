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

class AddCreatorIdToFlowTaskTable extends BaseMigration
{
    public function up(): void
    {
        Schema::tableIfExist('flow_task', function (SchemaBuilder $table) {
            $table->unsignedBigInteger('creator_id')->nullable()->after('project_id');
            $table->index('creator_id');
        });
    }

    public function down(): void
    {
        Schema::tableIfExist('flow_task', function (SchemaBuilder $table) {
            $table->dropColumn('creator_id');
        });
    }
}
