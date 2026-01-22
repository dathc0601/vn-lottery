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
        Schema::table('vietlott_results', function (Blueprint $table) {
            $table->unique(['game_type', 'draw_number'], 'vietlott_results_game_draw_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vietlott_results', function (Blueprint $table) {
            $table->dropUnique('vietlott_results_game_draw_unique');
        });
    }
};
