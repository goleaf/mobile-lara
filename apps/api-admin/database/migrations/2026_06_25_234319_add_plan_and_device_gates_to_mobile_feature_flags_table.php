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
        Schema::table('mobile_feature_flags', function (Blueprint $table) {
            $table->json('required_plans')
                ->nullable()
                ->after('minimum_app_version');
            $table->json('device_constraints')
                ->nullable()
                ->after('required_plans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobile_feature_flags', function (Blueprint $table) {
            $table->dropColumn(['required_plans', 'device_constraints']);
        });
    }
};
