<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * 2025_12_29_000001_add_creator_id_to_flow_task_table
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
        Schema::table('flow_task', function (SchemaBuilder $table) {
            $table->unsignedBigInteger('creator_id')->nullable()->after('project_id');
            $table->index('creator_id');
        });
    }

    public function down(): void
    {
        Schema::table('flow_task', function (SchemaBuilder $table) {
            $table->dropColumn('creator_id');
        });
    }
}
