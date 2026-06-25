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
        Schema::connection($this->connection)->create('media_items', function (Blueprint $table) {
            $table->id();
            $table->string('path', 2048);
            $table->string('type', 32)->index();
            $table->string('mime', 160)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->text('caption')->nullable();
            $table->string('sync_status', 32)->default('pending')->index();
            $table->string('related_entity_type')->nullable()->index();
            $table->string('related_entity_id')->nullable()->index();
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();

            $table->index(['type', 'created_at']);
            $table->index(['sync_status', 'created_at', 'id']);
            $table->index(['related_entity_type', 'related_entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('media_items');
    }
};
