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
            $table->string('applies_from_version', 40)
                ->nullable()
                ->after('cohort_key');
            $table->string('applies_until_version', 40)
                ->nullable()
                ->after('applies_from_version');

            $table->index(
                ['platform', 'is_active', 'applies_from_version', 'applies_until_version'],
                'mobile_app_version_range_lookup_index',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobile_app_version_policies', function (Blueprint $table) {
            $table->dropIndex('mobile_app_version_range_lookup_index');
            $table->dropColumn(['applies_from_version', 'applies_until_version']);
        });
    }
};
