<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar',
    'bio',
    'role',
    'is_active',
    'last_seen_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_seen_at' => 'datetime',
        ];
    }
    public function prayerRequests()
{
    return $this->hasMany(PrayerRequest::class);
}

public function prayerSupports()
{
    return $this->hasMany(PrayerSupport::class);
}

public function createdPrayerGroups()
{
    return $this->hasMany(PrayerGroup::class, 'created_by');
}

public function hostedPrayerRooms()
{
    return $this->hasMany(PrayerRoom::class, 'host_user_id');
}
}

