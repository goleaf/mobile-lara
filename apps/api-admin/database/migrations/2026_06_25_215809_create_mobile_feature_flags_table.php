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
        Schema::create('mobile_feature_flags', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->string('name');
            $table->string('default_state', 40)->default('disabled');
            $table->string('reason')->nullable();
            $table->string('message')->nullable();
            $table->string('minimum_app_version', 40)->nullable();
            $table->string('offline_behavior', 60)->default('online_only');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['default_state', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_feature_flags');
    }
};
