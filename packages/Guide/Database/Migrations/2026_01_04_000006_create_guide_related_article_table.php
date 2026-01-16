<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_04_000006_create_guide_related_article_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateGuideRelatedArticleTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->create('guide_related_article', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('guide_article_id');
            $table->unsignedBigInteger('related_article_id');
            $table->dateTimestamps();

            $table->foreign('guide_article_id')
                ->references('id')
                ->on('guide_article')
                ->onDelete('cascade');

            $table->foreign('related_article_id')
                ->references('id')
                ->on('guide_article')
                ->onDelete('cascade');

            $table->unique(['guide_article_id', 'related_article_id']);
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('guide_related_article');
    }
}
