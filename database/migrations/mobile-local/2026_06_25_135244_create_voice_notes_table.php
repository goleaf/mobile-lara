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
        Schema::connection($this->connection)->create('voice_notes', function (Blueprint $table) {
            $table->id();
            $table->string('local_file_path', 2048);
            $table->unsignedInteger('duration')->nullable();
            $table->text('transcript')->nullable();
            $table->string('sync_status', 32)->default('pending')->index();
            $table->string('related_entity_type')->nullable()->index();
            $table->string('related_entity_id')->nullable()->index();
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();

            $table->index(['sync_status', 'created_at', 'id']);
            $table->index(['related_entity_type', 'related_entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('voice_notes');
    }
};
