<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * 2026_01_04_000004_create_guide_feedback_table.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateGuideFeedbackTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->create('guide_feedback', function (SchemaBuilder $table) {
            $table->id();
            $table->unsignedBigInteger('guide_article_id');
            $table->integer('rating'); // 1-5 or 0/1 for helpfulness
            $table->text('comment')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->dateTimestamps();

            $table->foreign('guide_article_id')
                ->references('id')
                ->on('guide_article')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('guide_feedback');
    }
}
