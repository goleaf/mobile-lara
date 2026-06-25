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
        Schema::connection($this->connection)->create('scan_history', function (Blueprint $table) {
            $table->id();
            $table->string('scan_type', 32)->index();
            $table->text('raw_value');
            $table->json('parsed_value')->nullable();
            $table->text('action_result')->nullable();
            $table->string('status', 32)->default('captured')->index();
            $table->timestamp('created_at')->nullable()->index();

            $table->index(['scan_type', 'created_at', 'id']);
            $table->index(['status', 'created_at', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('scan_history');
    }
};
