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
        Schema::table('mobile_app_version_policies', function (Blueprint $table) {
            $table->foreignId('tenant_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();
            $table->string('cohort_key', 80)
                ->nullable()
                ->after('tenant_id');

            $table->index(['tenant_id', 'platform', 'is_active']);
            $table->index(['cohort_key', 'platform', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobile_app_version_policies', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropIndex(['tenant_id', 'platform', 'is_active']);
            $table->dropIndex(['cohort_key', 'platform', 'is_active']);
            $table->dropColumn(['tenant_id', 'cohort_key']);
        });
    }
};
