<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'description',
        'icon',
    ];

    // Relatie cu userii care au deblocat aceasta realizare
    public function user()
    {
        return $this->belongsToMany(User::class, 'user_achievements')
                    ->withTimestamps()
                    ->withPivot('unlocked_at');
    }
}
