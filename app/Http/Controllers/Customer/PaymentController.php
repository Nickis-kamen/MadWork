<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Devis;
use App\Models\Order;
use App\Models\Transaction;
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

        $price = $devis->price;
        $acompte = $price * 0.5; // 50% du prix total
        $commission = $acompte * 0.08; // 8% de commission
        $amountToPay = $acompte + $commission;
        $remainingAmount = $price - $acompte;
        return Inertia::render('Customer/Payment', [
            'transaction' => $transaction,
            'balance' => $balance,
            'price' => $price,
            'commission' => $commission,
            'amountToPay' => $amountToPay,
            'remainingAmount' => $remainingAmount
        ]);
    }

    public function payFirst(Devis $devis)
    {
        $devis->update([
        'status' => 'accepted'
        ]);

        $base = $devis->price * 0.5;
        $commission = $base * 0.08;
        $total = $base + $commission;

        $transaction = Transaction::create([
            'reference' => 'TXN-' . uniqid(),
            'user_id' => Auth::id(),
            'type' => 'purchase',
            'payment_stage' => 'first',
            'amount' => $total,
            'status' => 'pending',
            'description' => 'Acompte devis #' . $devis->reference,
        ]);

        $transaction->update([
            'status' => 'completed'
        ]);

        // créer order
        $order = Order::create([
            'client_id' => $devis->user_id,
            'agency_id' => $devis->agency_id,
            'service_id' => $devis->service_id,
            'devis_id' => $devis->id,
            'amount' => $devis->price,
            'commission' => $transaction->amount * 0.08,
            'status' => 'in_progress'
        ]);

        // lier transaction → order
        $transaction->update([
            'order_id' => $order->id
        ]);

        return redirect()->route('orders.show', $order->id);
    }
}
