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
        Schema::create('record_record_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('record_tag_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tenant_record_id', 'record_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('record_record_tag');
    }
};
