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
        Schema::create('mobile_diagnostic_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('mobile_device_session_id')->nullable()->constrained()->nullOnDelete();
            $table->string('public_id')->unique();
            $table->string('app_version')->nullable();
            $table->string('api_base_url')->nullable();
            $table->string('support_ticket_id')->nullable();
            $table->json('redactions_applied');
            $table->json('snapshot');
            $table->unsignedInteger('failed_sync_actions_count')->default(0);
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('received_at');
            $table->timestamps();

            $table->index(['tenant_id', 'received_at']);
            $table->index(['tenant_id', 'user_id', 'received_at']);
            $table->index(['mobile_device_session_id', 'received_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_diagnostic_reports');
    }
};
