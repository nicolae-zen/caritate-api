<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cause_id',
        'amount',
        'day_of_month',
        'status',
        'start_date',
        'next_charge_date',
        'payment_token',
        'last_payment_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'next_charge_date' => 'date',
    ];

    // RELAȚII
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cause()
    {
        return $this->belongsTo(Cause::class);
    }

    public function lastPayment()
    {
        return $this->belongsTo(Donation::class, 'last_payment_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}
