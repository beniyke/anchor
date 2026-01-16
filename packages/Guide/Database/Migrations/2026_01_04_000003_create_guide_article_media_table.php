<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_04_000003_create_guide_article_media_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateGuideArticleMediaTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->create('guide_article_media', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('guide_article_id');
            $table->unsignedBigInteger('media_id');
            $table->string('type')->default('attachment'); // attachment, gallery, featured
            $table->dateTimestamps();

            $table->foreign('guide_article_id')
                ->references('id')
                ->on('guide_article')
                ->onDelete('cascade');

            $table->foreign('media_id')
                ->references('id')
                ->on('media')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('guide_article_media');
    }
}
