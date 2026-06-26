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
        Schema::create('mobile_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('public_id')->unique();
            $table->string('type')->default('info');
            $table->string('title');
            $table->text('body');
            $table->json('data')->nullable();
            $table->string('deep_link')->nullable();
            $table->string('source')->default('system');
            $table->string('delivery_status')->default('queued');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'user_id', 'deleted_at', 'created_at']);
            $table->index(['tenant_id', 'user_id', 'read_at']);
            $table->index(['tenant_id', 'delivery_status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_notifications');
    }
};
