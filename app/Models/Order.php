<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
        'reference',
        'agency_id',
        'service_id',
        'devis_id',
        'amount',
        'commission',
        'progress',
        'progress_note',
        'status',
        'payment_status'
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

}
