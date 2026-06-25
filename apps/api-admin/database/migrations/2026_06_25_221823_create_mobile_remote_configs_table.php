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
        Schema::create('mobile_remote_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->string('category', 80)->default('mobile');
            $table->json('value');
            $table->string('version', 80)->default('global-default');
            $table->string('description')->nullable();
            $table->boolean('is_sensitive')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['category', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_remote_configs');
    }
};
