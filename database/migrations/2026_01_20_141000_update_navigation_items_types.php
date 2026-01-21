<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Alter enum to include BOTH old and new values
        DB::statement("ALTER TABLE navigation_items MODIFY COLUMN type ENUM('route', 'static_link', 'xsmb_days', 'province_central', 'province_south', 'xsmt_days', 'xsmn_days', 'divider') DEFAULT 'route'");

        // Step 2: Update existing data to new type names
        DB::table('navigation_items')
            ->where('type', 'province_central')
            ->update(['type' => 'xsmt_days']);

        DB::table('navigation_items')
            ->where('type', 'province_south')
            ->update(['type' => 'xsmn_days']);

        // Step 3: Update dropdown_type from 'none' to 'simple'
        DB::table('navigation_items')
            ->where('dropdown_type', 'none')
            ->update(['dropdown_type' => 'simple']);

        // Step 4: Alter enum to remove old values
        DB::statement("ALTER TABLE navigation_items MODIFY COLUMN type ENUM('route', 'static_link', 'xsmb_days', 'xsmt_days', 'xsmn_days', 'divider') DEFAULT 'route'");
        DB::statement("ALTER TABLE navigation_items MODIFY COLUMN dropdown_type ENUM('simple', 'mega_menu') DEFAULT 'simple'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Alter enum to include both old and new values
        DB::statement("ALTER TABLE navigation_items MODIFY COLUMN type ENUM('route', 'static_link', 'xsmb_days', 'province_central', 'province_south', 'xsmt_days', 'xsmn_days', 'divider') DEFAULT 'route'");
        DB::statement("ALTER TABLE navigation_items MODIFY COLUMN dropdown_type ENUM('none', 'simple', 'mega_menu') DEFAULT 'none'");

        // Step 2: Revert type names
        DB::table('navigation_items')
            ->where('type', 'xsmt_days')
            ->update(['type' => 'province_central']);

        DB::table('navigation_items')
            ->where('type', 'xsmn_days')
            ->update(['type' => 'province_south']);

        // Step 3: Restore old enum columns (remove new values)
        DB::statement("ALTER TABLE navigation_items MODIFY COLUMN type ENUM('route', 'static_link', 'xsmb_days', 'province_central', 'province_south', 'divider') DEFAULT 'route'");
    }
};

