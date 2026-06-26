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
        Schema::create('tenant_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('record_category_id')->nullable()->constrained('record_categories')->nullOnDelete();
            $table->string('public_id')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('draft');
            $table->string('priority')->default('normal');
            $table->json('metadata')->nullable();
            $table->string('sync_version');
            $table->timestamp('archived_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['tenant_id', 'status', 'updated_at']);
            $table->index(['tenant_id', 'priority', 'updated_at']);
            $table->index(['tenant_id', 'archived_at', 'updated_at']);
            $table->index(['tenant_id', 'record_category_id']);
            $table->index(['tenant_id', 'created_by_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_records');
    }
};
