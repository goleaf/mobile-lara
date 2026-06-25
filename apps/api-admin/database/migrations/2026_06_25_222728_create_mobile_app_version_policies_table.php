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
        Schema::create('mobile_app_version_policies', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 32)->default('all');
            $table->string('minimum_supported_version', 40)->default('1.0.0');
            $table->string('minimum_recommended_version', 40)->nullable();
            $table->string('latest_version', 40)->nullable();
            $table->json('blocked_versions')->nullable();
            $table->json('store_urls')->nullable();
            $table->string('message')->nullable();
            $table->string('support_url')->nullable();
            $table->boolean('force_update')->default(false);
            $table->boolean('maintenance_enabled')->default(false);
            $table->string('maintenance_message')->nullable();
            $table->unsignedInteger('retry_after_seconds')->nullable();
            $table->json('allowed_actions')->nullable();
            $table->boolean('logout_allowed')->default(true);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['platform', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_app_version_policies');
    }
};
