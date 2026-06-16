<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Devis;
use App\Models\Order;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Chiffre d'affaires total (somme des transactions de type 'sale' et 'deposit' complétées)
        $totalCA = Transaction::where('status', 'completed')
            ->whereIn('type', ['sale', 'deposit', 'subscription'])
            ->sum('amount');

        // CA du mois en cours
        $monthlyCA = Transaction::where('status', 'completed')
            ->whereIn('type', ['sale', 'deposit', 'subscription'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // Revenu total (inclut les commissions et frais)
        $sale = Transaction::where('status', 'completed')
            ->whereIn('type', ['sale'])
            ->sum('amount');

        $commission = $sale * 0.08;

        $withdrawals = Transaction::where('status', 'completed')
            ->where('type', 'withdrawal')
            ->sum('amount');

        $fees = $withdrawals * 0.04;

        $subscriptions = Transaction::where('status', 'completed')
            ->where('type', 'subscription')
            ->sum('amount');

        $totalRevenue = $commission + $fees + $subscriptions;

        // Revenu mensuel
        $monthlySale = Transaction::where('status', 'completed')
            ->whereIn('type', ['sale'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $monthlyCommission = $monthlySale * 0.08;

        $monthlyWithdrawals = Transaction::where('status', 'completed')
            ->where('type', 'withdrawal')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $monthlyFees = $monthlyWithdrawals * 0.04;

        $monthlySubscriptions = Transaction::where('status', 'completed')
            ->where('type', 'subscription')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $monthlyRevenue = $monthlyCommission + $monthlyFees + $monthlySubscriptions;

        // Paiements en attente (transactions pending)
        $pendingPayments = Transaction::where('status', 'pending')->count();

        // Nombre total d'agences
        $totalAgencies = Agency::count();

        // Nombre total de clients (utilisateurs avec rôle 'customer')
        $totalCustomers = User::where('role', 'customer')->count();

        // === STATISTIQUES PRINCIPALES (pour l'affichage) ===
        $stats = [
            'balance' => $user->balance ?? 0,
            'activeServices' => Service::count(),
            'totalAgencies' => $totalAgencies,
            'totalCustomers' => $totalCustomers
        ];

        // === REVENUS ===
        $revenue = [
            'totalCA' => $totalCA,
            'monthlyCA' => $monthlyCA,
            'totalRevenue' => $totalRevenue,
            'monthlyRevenue' => $monthlyRevenue,
            'pendingPayments' => $pendingPayments
        ];

        // === STATISTIQUES PROJETS ===
        $projectStats = [
            'inProgress' => Order::where('status', 'in_progress')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'delivered' => Order::where('status', 'delivered')->count()
        ];

        // === AGENCES (5 dernières) ===
        $agencies = Agency::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // === DERNIÈRES TRANSACTIONS (10 dernières avec infos agence) ===
        $transactions = Transaction::orderBy('created_at', 'desc')->with('user')
            ->limit(10)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'revenue' => $revenue,
            'projectStats' => $projectStats,
            'transactions' => $transactions,
            'agencies' => $agencies,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    }
}
