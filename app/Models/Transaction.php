<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    public function getRouteKeyName()
    {
        return 'reference';
    }

    protected $fillable = [
        'reference',
        'user_id',
        'type',
        'method',
        'number',
        'amount',
        'order_id',
        'subscription_id',
        'devis_id',
        'status',
        'description',
        'payment_stage',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }
}
