<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('record_id')->index();
            $table->unsignedBigInteger('media_item_id')->nullable()->index();
            $table->string('path');
            $table->string('name')->nullable();
            $table->string('mime')->nullable();
            $table->string('type')->default('file')->index();
            $table->unsignedBigInteger('size')->nullable();
            $table->text('caption')->nullable();
            $table->string('sync_status')->default('pending')->index();
            $table->string('upload_status')->default('queued')->index();
            $table->json('metadata')->default('{}');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['record_id', 'deleted_at', 'created_at', 'id']);
            $table->index(['record_id', 'sync_status']);
            $table->index(['record_id', 'upload_status']);
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
