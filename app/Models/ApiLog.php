<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'endpoint',
        'province_code',
        'response_status',
        'response_time_ms',
        'error_message',
        'fetched_count',
    ];
}
