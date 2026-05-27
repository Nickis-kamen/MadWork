<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    //
    public function index()
    {
        $subscriptions = Subscription::with('agency')->latest()->get();

        return Inertia::render('Admin/Subscription', [
            //
            'subscriptions' => $subscriptions
        ]);
    }

    public function active(Subscription $subscription)
    {
        if ($subscription->status === 'active') {
            return redirect('/admin/subscriptions')->with([
            'message' => 'Abonnement déjà activé',
            'type' => 'error'
        ]);
        }

        if($subscription->plan === 'basic') {
            $ends_at = now()->addMonth();
        } elseif ($subscription->plan === 'pro') {
            $ends_at = now()->addMonths(5);
        } else {
            $ends_at = now()->addYear();
        }


        $subscription->update([
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => $ends_at,
            ]);

        return redirect('/admin/subscriptions')->with([
            'message' => 'Abonnement activé avec succès',
            'type' => 'success'
        ]);

    }
}
