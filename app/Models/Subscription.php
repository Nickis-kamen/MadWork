<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'plan',
        'amount',
        'starts_at',
        'ends_at'
    ];

    public function agency()
    {
        return $this->belongsTo(User::class, 'agency_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
