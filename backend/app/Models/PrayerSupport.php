<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrayerSupport extends Model
{
    protected $fillable = [
        'prayer_request_id',
        'user_id',
        'prayed_at',
    ];

    protected $casts = [
        'prayed_at' => 'datetime',
    ];
}
