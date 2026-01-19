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
        Schema::create('lottery_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained()->onDelete('cascade');
            $table->string('turn_num'); // e.g., "17/01/2026"
            $table->date('draw_date'); // Parsed from turn_num
            $table->dateTime('draw_time'); // From API openTime
            $table->bigInteger('draw_timestamp')->nullable(); // From API openTimeStamp
            $table->string('open_num')->nullable(); // Quick reference numbers (e.g., "2,3,6,6,9,2")
            $table->string('prize_special')->nullable(); // ĐB - 6 digits
            $table->string('prize_1')->nullable(); // G1 - 5 digits
            $table->string('prize_2')->nullable(); // G2 - 5 digits
            $table->text('prize_3')->nullable(); // G3 - comma-separated (2 numbers)
            $table->text('prize_4')->nullable(); // G4 - comma-separated (7 numbers)
            $table->string('prize_5')->nullable(); // G5 - 4 digits
            $table->text('prize_6')->nullable(); // G6 - comma-separated (3 numbers)
            $table->string('prize_7')->nullable(); // G7 - 3 digits
            $table->string('prize_8')->nullable(); // G8 - 2 digits
            $table->json('detail_json')->nullable(); // Store original detail array for reference
            $table->integer('status')->default(0); // From API (2 = completed)
            $table->timestamps();

            // Indexes
            $table->index('province_id');
            $table->index('draw_date');
            $table->index('turn_num');
            $table->unique(['province_id', 'turn_num']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lottery_results');
    }
};
