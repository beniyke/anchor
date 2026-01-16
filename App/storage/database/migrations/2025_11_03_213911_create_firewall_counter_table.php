<?php
use Database\Migration\BaseMigration;
use Database\Schema\Schema;
use Database\Schema\SchemaBuilder;

class CreateFirewallCounterTable extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('firewall_counter')) {
            Schema::create('firewall_counter', function (SchemaBuilder $table) {
                $table->string('key_hash', 64)->unique();
                $table->integer('request_count')->default(0);
                $table->string('start_time', 30);
                $table->primary(['key_hash']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firewall_counter');
    }
}
