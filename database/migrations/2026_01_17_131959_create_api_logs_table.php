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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->text('endpoint'); // Full API URL
            $table->string('province_code')->nullable();
            $table->integer('response_status')->nullable(); // HTTP status code
            $table->integer('response_time_ms')->nullable(); // Response time in milliseconds
            $table->text('error_message')->nullable(); // If request failed
            $table->integer('fetched_count')->default(0); // Number of results fetched
            $table->timestamp('created_at')->nullable();

            // Indexes
            $table->index('province_code');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
