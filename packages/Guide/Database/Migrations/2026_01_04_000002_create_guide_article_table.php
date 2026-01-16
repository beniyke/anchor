<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_04_000002_create_guide_article_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateGuideArticleTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->create('guide_article', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid')->unique();
            $table->unsignedBigInteger('guide_category_id')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('view_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->dateTimestamps();

            $table->foreign('guide_category_id')
                ->references('id')
                ->on('guide_category')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('guide_article');
    }
}
