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
        Schema::create('mobile_sync_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('mobile_device_session_id')->nullable()->constrained()->nullOnDelete();
            $table->string('public_id')->unique();
            $table->string('client_batch_id')->nullable();
            $table->string('client_intent_id');
            $table->string('idempotency_key');
            $table->string('collection');
            $table->string('action');
            $table->string('target_public_id')->nullable();
            $table->string('base_sync_version')->nullable();
            $table->string('outcome');
            $table->string('error_code')->nullable();
            $table->string('error_message')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamp('processed_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'idempotency_key']);
            $table->index(['tenant_id', 'collection', 'processed_at']);
            $table->index(['tenant_id', 'outcome', 'processed_at']);
            $table->index(['tenant_id', 'client_batch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_sync_events');
    }
};
