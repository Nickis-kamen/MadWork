<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    //
    public function index()
    {
        $orders = Order::with('service', 'agency', 'devis')->where('client_id', Auth::id())->get();

        return Inertia::render('Customer/Order', [
            'orders' => $orders
        ]);
    }

    public function finalPayment(Order $order)
    {

        $base = $order->amount * 0.5;
        $commission = $base * 0.08;
        $total = $base + $commission;

        $transaction = Transaction::create([
            'reference' => 'TXN-' . uniqid(),
            'user_id' => Auth::id(),
            'type' => 'purchase',
            'payment_stage' => 'final',
            'amount' => $total,
            'status' => 'pending',
            'devis_id' => $order->devis_id,
            'order_id' => $order->id,
            'description' => 'Paiement final #' . $order->devis->reference,
        ]);

        Transaction::create([
            'reference' => 'TXN-' . uniqid(),
            'user_id' => $order->agency->user->id,
            'type' => 'sale',
            'payment_stage' => 'final',
            'amount' => $base,
            'status' => 'pending',
            'devis_id' => $order->devis_id,
            'order_id' => $order->id,
            'description' => 'Paiement final #' . $order->devis->reference,
        ]);

        $admin = User::where('role', 'admin')->first();

        Transaction::create([
            'reference' => 'TXN-' . uniqid(),
            'user_id' => $admin->id,
            'type' => 'sale',
            'payment_stage' => 'final',
            'amount' => $total,
            'status' => 'pending',
            'devis_id' => $order->devis_id,
            'order_id' => $order->id,
            'description' => 'Paiement final #' . $order->devis->reference,
        ]);

        return redirect('/payment/' . $transaction->reference);
    }
}
