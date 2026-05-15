<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Devis;
use App\Models\Order;
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

        $devis = $transaction->devis;
        $agency = $devis->agency;
        $service = $devis->service;

        $price = $devis->price;
        $acompte = $price * 0.5; // 50% du prix total
        $commission = $acompte * 0.08; // 8% de commission
        $amountToPay = $acompte + $commission;
        $remainingAmount = $price - $acompte;
        $paymentStage = $transaction->payment_stage;

        return Inertia::render('Customer/Payment', [
            'agency' => $agency,
            'service' => $service,
            'transaction' => $transaction,
            'devis' => $devis,
            'balance' => $balance,
            'price' => $price,
            'commission' => $commission,
            'amountToPay' => $amountToPay,
            'remainingAmount' => $remainingAmount,
            'paymentStage' => $paymentStage
        ]);
    }

    public function payFirst(Transaction $transaction)
    {
        $devis = $transaction->devis;
        $base = $devis->price * 0.5;
        $commission = $base * 0.08;
        // créer order
        $order = Order::create([
            'reference' => 'CMD-' . uniqid(),
            'client_id' => $devis->user_id,
            'agency_id' => $devis->agency_id,
            'service_id' => $devis->service_id,
            'devis_id' => $devis->id,
            'amount' => $devis->price,
            'commission' => $commission,
            'status' => 'pending',
            'payment_status' => 'unpaid'
            ]);
        $balance = Auth::user()->balance ? Auth::user()->balance->amount : 0;

        $user_agency = $devis->agency->user;
        $admin = User::where('role', 'admin')->first();
        $balanceAgency = Balance::where('user_id', $user_agency->id);
        $balanceAdmin = Balance::where('user_id', $admin->id);

        if ($balance < $transaction->amount) {
            return redirect('/balance')->with([
                'message' => 'Solde insuffisant pour effectuer le paiement. Veuillez recharger votre balance.',
                'type' => 'error',
            ]);
        }

        Balance::where('user_id', Auth::id())->decrement('amount', $transaction->amount);

        if (!$balanceAgency->exists()) {
            Balance::create([
                'user_id' => $user_agency->id,
                'amount' => $base,
            ]);
        } else {
            $balanceAgency->increment('amount', $base);
        }
        if (!$balanceAdmin->exists()) {
            Balance::create([
                'user_id' => $admin->id,
                'amount' => $commission,
            ]);
        } else {
            $balanceAdmin->increment('amount', $commission);
        }

        $transaction->update([
            'order_id' => $order->id,
            'status' => 'completed'
        ]);

        $transaction_agency = Transaction::where('user_id', $user_agency->id)->where('devis_id', $devis->id);

        $transaction_agency->update([
            'order_id' => $order->id,
            'status' => 'completed'
        ]);

        $transaction_admin = Transaction::where('user_id', $admin->id)->where('devis_id', $devis->id);

        $transaction_admin->update([
            'order_id' => $order->id,
            'status' => 'completed'
        ]);

        return redirect('/balance')->with([
            'message' => 'Paiement effectué avec succès',
            'type' => 'success', // ou 'error', 'warning', 'info'
        ]);
    }

    public function payFinal(Transaction $transaction)
    {
        $devis = $transaction->devis;
        $base = $devis->price * 0.5;
        $commission = $base * 0.08;

        $order = $transaction->order;
    // dd($order);
        $balance = Auth::user()->balance ? Auth::user()->balance->amount : 0;

        $user_agency = $devis->agency->user;
        $admin = User::where('role', 'admin')->first();
        $balanceAgency = Balance::where('user_id', $user_agency->id);
        $balanceAdmin = Balance::where('user_id', $admin->id);

        if ($balance < $transaction->amount) {
            return redirect('/balance')->with([
                'message' => 'Solde insuffisant pour effectuer le paiement. Veuillez recharger votre balance.',
                'type' => 'error',
            ]);
        }

        Balance::where('user_id', Auth::id())->decrement('amount', $transaction->amount);

        if (!$balanceAgency->exists()) {
            Balance::create([
                'user_id' => $user_agency->id,
                'amount' => $base,
            ]);
        } else {
            $balanceAgency->increment('amount', $base);
        }
        if (!$balanceAdmin->exists()) {
            Balance::create([
                'user_id' => $admin->id,
                'amount' => $commission,
            ]);
        } else {
            $balanceAdmin->increment('amount', $commission);
        }

        $transaction->update([
            'status' => 'completed'
        ]);

        $transaction_agency = Transaction::where('user_id', $user_agency->id)->where('devis_id', $devis->id);

        $transaction_agency->update([
            'status' => 'completed'
        ]);

        $transaction_admin = Transaction::where('user_id', $admin->id)->where('devis_id', $devis->id);

        $transaction_admin->update([
            'status' => 'completed'
        ]);

        $order->update([
            'payment_status' => 'paid'
        ]);

        return redirect('/balance')->with([
            'message' => 'Paiement effectué avec succès',
            'type' => 'success', // ou 'error', 'warning', 'info'
        ]);
    }
}
