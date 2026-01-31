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
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->string('region', 20); // 'north', 'central', 'south'
            $table->date('prediction_date');
            $table->date('reference_date'); // Previous day's lottery used for analysis
            $table->foreignId('article_id')->nullable()->constrained()->nullOnDelete();
            $table->json('predictions_data'); // {head_tail, loto_2_digit, loto_3_digit, vip_4_digit}
            $table->json('analysis_data'); // {bach_thu, lat_lien_tuc, cau_2_nhay, pascal, lo_kep, loto_hay_ve}
            $table->json('statistics_snapshot'); // Frequency/gap data at generation time
            $table->json('lottery_results_snapshot'); // Previous day's results for display
            $table->enum('status', ['pending', 'generated', 'published'])->default('pending');
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['region', 'prediction_date']);
            $table->index(['status', 'prediction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
