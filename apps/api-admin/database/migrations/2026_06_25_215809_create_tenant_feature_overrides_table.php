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
        Schema::create('tenant_feature_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('feature_key', 80);
            $table->string('state', 40);
            $table->string('reason')->nullable();
            $table->string('message')->nullable();
            $table->string('offline_behavior', 60)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'feature_key']);
            $table->index(['feature_key', 'state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_feature_overrides');
    }
};
