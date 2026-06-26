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
        Schema::create('mobile_support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mobile_support_ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('public_id')->unique();
            $table->text('body');
            $table->string('direction')->default('user');
            $table->string('visibility')->default('requester');
            $table->json('attachments')->nullable();
            $table->string('diagnostic_report_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'mobile_support_ticket_id', 'created_at']);
            $table->index(['tenant_id', 'author_user_id', 'created_at']);
            $table->index(['tenant_id', 'diagnostic_report_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_support_messages');
    }
};
