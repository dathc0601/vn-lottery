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
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Full province name (e.g., "Quảng Ngãi")
            $table->string('code')->unique(); // API gameCode (e.g., "qung")
            $table->enum('region', ['north', 'central', 'south']);
            $table->string('slug')->unique(); // URL-friendly name (e.g., "quang-ngai")
            $table->json('draw_days')->nullable(); // Days of week when draws occur [1,2,3...] (1=Monday)
            $table->time('draw_time')->nullable(); // e.g., "17:15:00"
            $table->integer('sort_order')->default(0); // Display order
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};
