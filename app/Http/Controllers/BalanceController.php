<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BalanceController extends Controller
{
    //
    public function index()
    {
        $balance = Auth::user()->balance ? Auth::user()->balance->amount : 0;

        $transactions = Transaction::where('user_id', Auth::id())->with('order.agency', 'order.service', 'order.client')->orderBy('created_at', 'desc')->get();

        // dd($transactions);
        return Inertia::render('Balance', [
            'balance' => $balance,
            'transactions' => $transactions
        ]);
    }

    public function deposit(Request $request)
    {
        // Validate the request
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:mvola,airtelmoney,orangemoney,visa',
            'number' => 'required|string|max:255',
        ]);

        // Create a new transaction
        Transaction::create([
            'reference' => 'TXN-' . uniqid(),
            'user_id' => Auth::id(),
            'type' => 'deposit',
            'method' => $request->method,
            'number' => $request->number,
            'amount' => $request->amount,
            'status' => 'pending',
        ]);

        // Update the user's balance
        $balance = Auth::user()->balance;

        if (!$balance) {
            Balance::create([
                'user_id' => Auth::id(),
                'amount' => $request->amount,
            ]);
        }else {
            $balance->increment('amount', $request->amount);
        }

        return redirect('/balance')->with([
            'message' => 'Depôt effectué avec succès',
            'type' => 'success'
        ]);
    }

    public function withdraw(Request $request)
    {
        // Validate the request
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:mvola,airtelmoney,orangemoney,visa',
            'number' => 'required|string|max:255',
        ]);

        // Check if the user has enough balance
        $balance = Auth::user()->balance;

        if (!$balance || $balance->amount < $request->amount) {
            return redirect('/balance')->with([
                'message' => 'Solde insuffisant pour effectuer ce retrait',
                'type' => 'error'
            ]);
        }

        // Create a new transaction
        Transaction::create([
            'reference' => 'TXN-' . uniqid(),
            'user_id' => Auth::id(),
            'type' => 'withdrawal',
            'method' => $request->method,
            'number' => $request->number,
            'amount' => -$request->amount,
            'status' => 'pending',
        ]);

        // Update the user's balance
        $balance->decrement('amount', $request->amount);

        return redirect('/balance')->with([
            'message' => 'Retrait effectué avec succès',
            'type' => 'success'
        ]);
    }
}
