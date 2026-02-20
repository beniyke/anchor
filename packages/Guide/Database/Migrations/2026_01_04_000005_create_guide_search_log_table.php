<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateGuideSearchLogTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->createIfNotExists('guide_search_log', function (SchemaBuilder $table) {
            $table->id();
            $table->string('query');
            $table->integer('results_count')->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('metadata')->nullable();
            $table->dateTimestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('guide_search_log');
    }
}
