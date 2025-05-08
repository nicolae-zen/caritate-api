<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cause extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'goal_amount',
        'is_active',
    ];

    // Relatii
    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}
