<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Province;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Central Region Fixes
        Province::where('code', 'phye')->update(['draw_days' => [1]]); // Phú Yên: Monday
        Province::where('code', 'thth')->update(['draw_days' => [1, 7]]); // Thừa Thiên Huế: Monday, Sunday
        Province::where('code', 'dalak')->update(['draw_days' => [2]]); // Đắk Lắk: Tuesday
        Province::where('code', 'quna')->update(['draw_days' => [2]]); // Quảng Nam: Tuesday
        Province::where('code', 'dana')->update(['draw_days' => [3, 6]]); // Đà Nẵng: Wednesday, Saturday
        Province::where('code', 'bidi')->update(['draw_days' => [4]]); // Bình Định: Thursday
        Province::where('code', 'qubi')->update(['draw_days' => [4]]); // Quảng Bình: Thursday
        Province::where('code', 'qutr')->update(['draw_days' => [4]]); // Quảng Trị: Thursday
        Province::where('code', 'gila')->update(['draw_days' => [5]]); // Gia Lai: Friday (CRITICAL FIX)
        Province::where('code', 'nith')->update(['draw_days' => [5]]); // Ninh Thuận: Friday (CRITICAL FIX)

        // South Region Fixes
        Province::where('code', 'cama')->update(['draw_days' => [1]]); // Cà Mau: Monday
        Province::where('code', 'doth')->update(['draw_days' => [1]]); // Đồng Tháp: Monday
        Province::where('code', 'tphc')->update(['draw_days' => [1, 6]]); // Hồ Chí Minh: Monday, Saturday
        Province::where('code', 'bali')->update(['draw_days' => [2]]); // Bạc Liêu: Tuesday
        Province::where('code', 'vuta')->update(['draw_days' => [2]]); // Vũng Tàu: Tuesday
        Province::where('code', 'cath')->update(['draw_days' => [3]]); // Cần Thơ: Wednesday
        Province::where('code', 'dona')->update(['draw_days' => [3]]); // Đồng Nai: Wednesday
        Province::where('code', 'angi')->update(['draw_days' => [4]]); // An Giang: Thursday
        Province::where('code', 'bith')->update(['draw_days' => [4]]); // Bình Thuận: Thursday
        Province::where('code', 'tani')->update(['draw_days' => [4]]); // Tây Ninh: Thursday
        Province::where('code', 'bidu')->update(['draw_days' => [5]]); // Bình Dương: Friday
        Province::where('code', 'trvi')->update(['draw_days' => [5]]); // Trà Vinh: Friday
        Province::where('code', 'vilo')->update(['draw_days' => [5]]); // Vĩnh Long: Friday

        // Add Bến Tre if missing
        if (!Province::where('code', 'betre')->exists()) {
            Province::create([
                'name' => 'Bến Tre',
                'code' => 'betre',
                'region' => 'south',
                'slug' => 'ben-tre',
                'draw_days' => [2], // Tuesday
                'draw_time' => '16:15:00',
                'sort_order' => 44.5,
                'is_active' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old (incorrect) values - Central Region
        Province::where('code', 'phye')->update(['draw_days' => [2]]);
        Province::where('code', 'thth')->update(['draw_days' => [2, 5]]);
        Province::where('code', 'dalak')->update(['draw_days' => [3]]);
        Province::where('code', 'quna')->update(['draw_days' => [3]]);
        Province::where('code', 'dana')->update(['draw_days' => [3, 7]]);
        Province::where('code', 'bidi')->update(['draw_days' => [5]]);
        Province::where('code', 'qubi')->update(['draw_days' => [5]]);
        Province::where('code', 'qutr')->update(['draw_days' => [5]]);
        Province::where('code', 'gila')->update(['draw_days' => [6]]);
        Province::where('code', 'nith')->update(['draw_days' => [6]]);

        // Revert to old (incorrect) values - South Region
        Province::where('code', 'cama')->update(['draw_days' => [2]]);
        Province::where('code', 'doth')->update(['draw_days' => [2]]);
        Province::where('code', 'tphc')->update(['draw_days' => [2, 7]]);
        Province::where('code', 'bali')->update(['draw_days' => [3]]);
        Province::where('code', 'vuta')->update(['draw_days' => [3]]);
        Province::where('code', 'cath')->update(['draw_days' => [4]]);
        Province::where('code', 'dona')->update(['draw_days' => [4]]);
        Province::where('code', 'angi')->update(['draw_days' => [5]]);
        Province::where('code', 'bith')->update(['draw_days' => [5]]);
        Province::where('code', 'tani')->update(['draw_days' => [5]]);
        Province::where('code', 'bidu')->update(['draw_days' => [6]]);
        Province::where('code', 'trvi')->update(['draw_days' => [6]]);
        Province::where('code', 'vilo')->update(['draw_days' => [6]]);

        // Remove Bến Tre
        Province::where('code', 'betre')->delete();
    }
};
