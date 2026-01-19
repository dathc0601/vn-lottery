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
        Schema::create('number_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained()->onDelete('cascade');
            $table->string('number', 2); // 2-digit number (00-99)
            $table->integer('frequency_30d')->default(0);
            $table->integer('frequency_60d')->default(0);
            $table->integer('frequency_90d')->default(0);
            $table->integer('frequency_100d')->default(0);
            $table->integer('frequency_200d')->default(0);
            $table->integer('frequency_300d')->default(0);
            $table->integer('frequency_500d')->default(0);
            $table->date('last_appeared')->nullable();
            $table->integer('cycle_count')->default(0); // Days since last appearance
            $table->timestamp('updated_at')->nullable();

            // Indexes
            $table->index('province_id');
            $table->index('number');
            $table->unique(['province_id', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('number_statistics');
    }
};
