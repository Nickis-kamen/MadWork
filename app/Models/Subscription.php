<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory;

    protected $fillable = [
        'reference',
        'agency_id',
        'plan',
        'amount',
        'status',
        'payment_status',
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
