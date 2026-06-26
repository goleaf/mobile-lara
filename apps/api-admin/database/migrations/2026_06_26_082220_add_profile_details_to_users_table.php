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
        Schema::table('users', function (Blueprint $table): void {
            $table->string('username', 30)->nullable()->after('avatar_path');
            $table->string('phone', 32)->nullable()->after('username');
            $table->text('bio')->nullable()->after('phone');
            $table->string('location', 80)->nullable()->after('bio');
            $table->string('website')->nullable()->after('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'username',
                'phone',
                'bio',
                'location',
                'website',
            ]);
        });
    }
};
