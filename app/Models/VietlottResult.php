<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class VietlottResult extends Model
{
    protected static function boot(): void
    {
        parent::boot();

        static::saved(function () {
            Cache::forget('sitemap_index');
            Cache::forget('sitemap_vietlott');
        });
    }

    protected $fillable = [
        'game_type',
        'draw_number',
        'draw_date',
        'jackpot_amount',
        'winning_numbers',
        'prize_breakdown',
    ];

    protected $casts = [
        'draw_date' => 'date',
        'winning_numbers' => 'array',
        'prize_breakdown' => 'array',
    ];
}
