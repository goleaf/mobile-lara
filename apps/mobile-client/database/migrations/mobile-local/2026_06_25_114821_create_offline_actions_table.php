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
        Schema::connection($this->connection)->create('offline_actions', function (Blueprint $table) {
            $table->id();
            $table->string('action_type')->index();
            $table->string('endpoint', 2048);
            $table->string('method', 16)->default('POST');
            $table->json('payload')->nullable();
            $table->json('headers')->nullable();
            $table->string('status', 32)->default('pending')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('available_at')->nullable()->index();
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable()->index();

            $table->index(['status', 'available_at', 'id']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('offline_actions');
    }
};
