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
        Schema::connection($this->connection)->table('offline_actions', function (Blueprint $table) {
            $table->string('local_version')->nullable()->index();
            $table->string('remote_version')->nullable()->index();
            $table->string('conflict_status', 32)->default('none')->index();
            $table->json('conflict_payload')->nullable();

            $table->index(['conflict_status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->table('offline_actions', function (Blueprint $table) {
            $table->dropIndex(['conflict_status', 'created_at']);
            $table->dropIndex(['local_version']);
            $table->dropIndex(['remote_version']);
            $table->dropIndex(['conflict_status']);
            $table->dropColumn([
                'local_version',
                'remote_version',
                'conflict_status',
                'conflict_payload',
            ]);
        });
    }
};
