<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    
    public function getJWTIdentifier()
    {
        return $this->getKey(); // id-ul userului
    }

    public function getJWTCustomClaims()
    {
        return []; // dacă vrei să adaugi date extra în token, aici
    }


    protected $fillable = [
        'phone_number',
        'name',
        'email',
        'is_admin',
        'total_donated',
        'last_login',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'total_donated' => 'decimal:2',
        'last_login' => 'datetime',
    ];

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
                    ->withTimestamps()
                    ->withPivot('unlocked_at');
    }
}
