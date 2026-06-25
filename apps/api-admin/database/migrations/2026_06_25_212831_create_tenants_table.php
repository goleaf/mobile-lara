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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status', 40)->default('active');
            $table->string('subscription_state', 40)->default('active');
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index(['status', 'id']);
            $table->index(['subscription_state', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
