<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();
        $subscriptions = Subscription::where('agency_id', $user->id)->latest()->get();

        return Inertia::render('Agency/Subscription', [
            'subscriptions' => $subscriptions
        ]);
    }

    public function plan(string $plan)
    {
        if(!in_array($plan, ['basic', 'pro', 'premium'])) {
            abort(404);
        }

        if($plan === 'basic') {
            $amount = 50000;
        } elseif($plan === 'pro') {
            $amount = 200000;
        } else {
            $amount = 425000;
        }

        $transaction = Transaction::create([
            'reference' => 'TXN-' . uniqid(),
            'user_id' => Auth::id(),
            'type' => 'subscription',
            'plan' => $plan,
            'amount' => $amount,
            'status' => 'pending',
            'description' => 'Abonnement ' . $plan,
        ]);

        return redirect('/agency/payment/' . $transaction->reference);
    }
}
