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
        Schema::create('tenant_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 60)->default('mobile_user');
            $table->string('status', 40)->default('active');
            $table->boolean('is_current')->default(false);
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'user_id']);
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'is_current']);
            $table->index(['tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_users');
    }
};
