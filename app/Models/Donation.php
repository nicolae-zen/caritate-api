<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cause_id',
        'amount',
        'currency',
        'status',
        'payment_gateway',
        'payment_reference',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    // Relatii
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cause()
    {
        return $this->belongsTo(Cause::class);
    }
}
