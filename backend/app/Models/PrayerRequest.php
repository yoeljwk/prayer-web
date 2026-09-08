<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrayerRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'prayer_group_id',
        'content',
        'visibility',
        'is_anonymous',
        'status',
        'answered_at',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'answered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(PrayerGroup::class, 'prayer_group_id');
    }

    public function supports()
    {
        return $this->hasMany(PrayerSupport::class);
    }

    public function comments()
    {
        return $this->hasMany(PrayerComment::class);
    }
}
