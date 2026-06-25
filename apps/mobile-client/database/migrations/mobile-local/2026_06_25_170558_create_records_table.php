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
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('draft')->index();
            $table->string('priority')->default('normal')->index();
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->timestamp('due_at')->nullable()->index();
            $table->json('metadata')->default('{}');
            $table->timestamp('archived_at')->nullable()->index();
            $table->softDeletes();
            $table->string('sync_status')->default('pending')->index();
            $table->timestamps();

            $table->index(['archived_at', 'updated_at', 'id']);
            $table->index(['status', 'priority', 'due_at']);
            $table->index(['user_id', 'category_id']);
            $table->index(['sync_status', 'updated_at']);
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};
