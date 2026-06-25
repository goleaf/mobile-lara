<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mobile_local';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection($this->connection)->create('mobile_local_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action')->index();
            $table->string('entity_type')->nullable()->index();
            $table->string('entity_id')->nullable()->index();
            $table->text('message');
            $table->json('metadata')->nullable();
            $table->string('sync_status', 32)->default('pending')->index();
            $table->timestamp('created_at')->nullable()->index();

            $table->index(['sync_status', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('mobile_local_activity_logs');
    }
};
