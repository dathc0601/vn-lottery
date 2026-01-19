<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotteryResult extends Model
{
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
