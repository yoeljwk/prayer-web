<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrayerGroupMember extends Model
{
    protected $fillable = [
        'prayer_group_id',
        'user_id',
        'role',
        'status',
        'joined_at',
    ];

    public function group()
    {
        return $this->belongsTo(PrayerGroup::class, 'prayer_group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
