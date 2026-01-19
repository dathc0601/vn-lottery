<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NumberStatistic extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'province_id',
        'number',
        'frequency_30d',
        'frequency_60d',
        'frequency_90d',
        'frequency_100d',
        'frequency_200d',
        'frequency_300d',
        'frequency_500d',
        'last_appeared',
        'cycle_count',
    ];

    protected $casts = [
        'last_appeared' => 'date',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
