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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('record_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->text('body');
            $table->string('sync_status')->default('pending')->index();
            $table->json('metadata')->default('{}');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['record_id', 'deleted_at', 'created_at', 'id']);
            $table->index(['record_id', 'sync_status']);
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
