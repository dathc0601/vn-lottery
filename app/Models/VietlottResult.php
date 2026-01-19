<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VietlottResult extends Model
{
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
