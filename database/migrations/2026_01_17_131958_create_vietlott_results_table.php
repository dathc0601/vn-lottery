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
        Schema::create('vietlott_results', function (Blueprint $table) {
            $table->id();
            $table->enum('game_type', ['mega645', 'power655', 'max3d', 'max3dpro']);
            $table->string('draw_number');
            $table->date('draw_date');
            $table->bigInteger('jackpot_amount')->default(0);
            $table->json('winning_numbers')->nullable();
            $table->json('prize_breakdown')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('game_type');
            $table->index('draw_date');
            $table->unique(['game_type', 'draw_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vietlott_results');
    }
};
