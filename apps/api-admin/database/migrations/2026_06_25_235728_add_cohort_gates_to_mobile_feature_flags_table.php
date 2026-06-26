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
            $table->json('allowed_cohorts')
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
            $table->dropColumn('allowed_cohorts');
        });
    }
};
