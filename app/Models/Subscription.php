<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory;

    public function getRouteKeyName()
    {
        return 'reference';
    }

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

    // Vérifier automatiquement à chaque récupération
    protected static function booted()
    {
        static::retrieved(function ($subscription) {
            // Vérifier si l'abonnement est actif
            if ($subscription->status !== 'active') {
                return;
            }

            // Vérifier si ends_at existe
            if (!$subscription->ends_at) {
                return;
            }

            // Vérifier si la date est dépassée
            if (Carbon::now()->gt($subscription->ends_at)) {
                // Mise à jour silencieuse
                $subscription->updateQuietly(['status' => 'finished']);

            }
        });
    }

    public function agency()
    {
        return $this->belongsTo(User::class, 'agency_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
