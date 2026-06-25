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
        Schema::connection($this->connection)->create('local_notification_schedules', function (Blueprint $table) {
            $table->string('notification_id')->primary();
            $table->string('title');
            $table->text('body');
            $table->string('type', 32)->default('info')->index();
            $table->json('data')->nullable();
            $table->string('deep_link', 2048)->nullable();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->string('status', 32)->default('scheduled')->index();
            $table->string('driver', 64)->default('placeholder')->index();
            $table->string('native_id')->nullable()->index();
            $table->timestamp('cancelled_at')->nullable()->index();
            $table->timestamp('created_at')->nullable()->index();

            $table->index(['status', 'scheduled_at', 'notification_id']);
            $table->index(['driver', 'status', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('local_notification_schedules');
    }
};
