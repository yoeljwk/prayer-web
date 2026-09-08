<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrayerGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'created_by',
        'name',
        'slug',
        'description',
        'avatar',
        'visibility',
        'invite_code',
        'max_members',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->hasMany(PrayerGroupMember::class);
    }

    public function prayerRequests()
    {
        return $this->hasMany(PrayerRequest::class);
    }

    public function rooms()
    {
        return $this->hasMany(PrayerRoom::class);
    }
}