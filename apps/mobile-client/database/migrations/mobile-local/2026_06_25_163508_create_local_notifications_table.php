<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mobile_local';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection($this->connection)->create('local_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('type', 32)->default('info')->index();
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamp('opened_at')->nullable()->index();
            $table->string('deep_link', 2048)->nullable();
            $table->timestamp('created_at')->nullable()->index();

            $table->index(['read_at', 'created_at', 'id']);
            $table->index(['type', 'created_at', 'id']);
            $table->index(['opened_at', 'created_at', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('local_notifications');
    }
};
