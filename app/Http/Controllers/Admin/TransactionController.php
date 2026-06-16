<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TransactionController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();
        $transactions = Transaction::with('order', 'user', 'subscription')
        ->where('user_id', '!=', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();

        return Inertia::render('Admin/Transactions', [
            'transactions' => $transactions
        ]);
    }
}
