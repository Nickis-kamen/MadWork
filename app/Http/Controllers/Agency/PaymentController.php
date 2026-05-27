<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PaymentController extends Controller
{
    //
    public function index(Transaction $transaction)
    {
        $balance = Auth::user()->balance ? Auth::user()->balance->amount : 0;
        $plan = $transaction->plan;

        return Inertia::render('Agency/Payment', [
            'balance' => $balance,
            'planId' => $plan,
            'transaction' => $transaction
        ]);
    }

    public function paySubscription(Transaction $transaction)
    {
        $subscription = Subscription::create([
                'reference' => 'SUB-' . uniqid(),
                'agency_id' => Auth::id(),
                'plan' => $transaction->plan,
                'amount' => $transaction->amount,
                'status' => 'pending',
                'payment_status' => 'unpaid'
            ]);

        $admin = User::where('role', 'admin')->first();
        $balanceAdmin = Balance::where('user_id', $admin->id);
        $balance = Auth::user()->balance ? Auth::user()->balance->amount : 0;

        if ($balance < $transaction->amount) {
            return redirect('/balance')->with([
                'message' => 'Solde insuffisant pour effectuer le paiement. Veuillez recharger votre balance.',
                'type' => 'error',
            ]);
        }

        Balance::where('user_id', Auth::id())->decrement('amount', $transaction->amount);

        if (!$balanceAdmin->exists()) {
            Balance::create([
                'user_id' => $admin->id,
                'amount' => $transaction->amount
            ]);
        } else {
            $balanceAdmin->increment('amount', $transaction->amount);
        }

        $transaction->update([
            'subscription_id' => $subscription->id,
            'status' => 'completed'
        ]);

        Transaction::create([
            'reference' => 'TXN-' . uniqid(),
            'user_id' => $admin->id,
            'type' => 'subscription',
            'subscription_id' => $subscription->id,
            'plan' => $transaction->plan,
            'amount' => $transaction->amount,
            'status' => 'completed',
            'description' => 'Abonnement ' . $transaction->plan,
        ]);

        $subscription->update([
            'payment_status' => 'paid',
        ]);

        return redirect('/balance')->with([
            'message' => 'Votre abonnement est en cours de traitement. Vous recevrez une notification une fois activé.',
            'type' => 'info', // ou 'error', 'warning', 'info'
        ]);
    }
}
