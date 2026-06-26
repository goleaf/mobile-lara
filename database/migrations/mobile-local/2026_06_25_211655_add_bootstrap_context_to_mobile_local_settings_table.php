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
        Schema::connection($this->connection)->table('mobile_local_settings', function (Blueprint $table): void {
            $table->json('bootstrap_context')->nullable()->after('sync_settings');
            $table->timestamp('bootstrap_cached_at')->nullable()->after('bootstrap_context')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->table('mobile_local_settings', function (Blueprint $table): void {
            $table->dropIndex(['bootstrap_cached_at']);
            $table->dropColumn(['bootstrap_context', 'bootstrap_cached_at']);
        });
    }
};
