<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LotteryResult extends Model
{
    protected static function boot(): void
    {
        parent::boot();

        static::saved(function (LotteryResult $result) {
            Cache::forget('sitemap_index');
            // Clear current month results cache
            if ($result->draw_date) {
                $yearMonth = Carbon::parse($result->draw_date)->format('Y-m');
                Cache::forget("sitemap_results_{$yearMonth}");
            }
        });
    }

    protected $fillable = [
        'province_id',
        'turn_num',
        'draw_date',
        'draw_time',
        'draw_timestamp',
        'open_num',
        'prize_special',
        'prize_1',
        'prize_2',
        'prize_3',
        'prize_4',
        'prize_5',
        'prize_6',
        'prize_7',
        'prize_8',
        'detail_json',
        'status',
    ];

    protected $casts = [
        'draw_date' => 'date',
        'draw_time' => 'datetime',
        'detail_json' => 'array',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
