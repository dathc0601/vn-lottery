<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Cache;

class Province extends Model
{
    protected $fillable = [
        'name',
        'code',
        'region',
        'slug',
        'draw_days',
        'draw_time',
        'sort_order',
        'is_active',
        'show_in_left_sidebar',
        'left_sidebar_order',
    ];

    protected $casts = [
        'draw_days' => 'array',
        'is_active' => 'boolean',
        'show_in_left_sidebar' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function () {
            Cache::forget('footer_schedule_data');
            Cache::forget('sitemap_index');
            Cache::forget('sitemap_provinces');
        });
    }

    public function lotteryResults()
    {
        return $this->hasMany(LotteryResult::class);
    }

    public function numberStatistics()
    {
        return $this->hasMany(NumberStatistic::class);
    }

    /**
     * Get total lottery results count
     */
    protected function totalResults(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->lotteryResults()->count(),
        );
    }

    /**
     * Get last update time (from most recent lottery result)
     */
    protected function lastUpdate(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->lotteryResults()
                ->latest('updated_at')
                ->value('updated_at'),
        );
    }

    /**
     * Get human-readable last update time
     */
    protected function lastUpdateHuman(): Attribute
    {
        return Attribute::make(
            get: function () {
                $lastUpdate = $this->last_update;

                if (!$lastUpdate) {
                    return 'Chưa có dữ liệu';
                }

                return $lastUpdate->locale('vi')->diffForHumans();
            }
        );
    }
}
