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
        Schema::create('mobile_push_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mobile_device_session_id')->nullable()->constrained()->nullOnDelete();
            $table->string('public_id')->unique();
            $table->string('token_hash');
            $table->string('token_preview');
            $table->string('provider');
            $table->string('platform');
            $table->string('device_id')->nullable();
            $table->string('app_version')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('last_registered_at');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'token_hash']);
            $table->index(['tenant_id', 'user_id', 'revoked_at']);
            $table->index(['mobile_device_session_id', 'revoked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_push_tokens');
    }
};
