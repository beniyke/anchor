<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Database\Migration\BaseMigration;
use Database\Schema\SchemaBuilder;

class CreateGuideCategoryTable extends BaseMigration
{
    public function up(): void
    {
        $this->schema()->createIfNotExists('guide_category', function (SchemaBuilder $table) {
            $table->id();
            $table->string('refid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->dateTimestamps();

            $table->foreign('parent_id')
                ->references('id')
                ->on('guide_category')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('guide_category');
    }
}
