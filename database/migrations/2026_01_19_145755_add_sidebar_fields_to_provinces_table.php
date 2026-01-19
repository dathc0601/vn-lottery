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
        Schema::table('provinces', function (Blueprint $table) {
            $table->boolean('show_in_left_sidebar')->default(false)->after('is_active');
            $table->integer('left_sidebar_order')->default(0)->after('show_in_left_sidebar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provinces', function (Blueprint $table) {
            $table->dropColumn(['show_in_left_sidebar', 'left_sidebar_order']);
        });
    }
};
