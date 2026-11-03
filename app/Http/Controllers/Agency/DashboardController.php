<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Devis;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();

        $agencyId = $user->agency?->id;
        // Statistiques principales
        $stats = [
            'balance' => $user->balance ? $user->balance->amount : 0,
            'activeServices' => $user->agency?->services()->count(),
            'pendingQuotes' => Devis::where('agency_id', $agencyId)->where('status', 'pending')->count()
        ];

        // Statistiques projets
        $projectStats = [
            'inProgress' => Order::where('agency_id', $agencyId)->where('status', 'in_progress')->count(),
            'completed' => Order::where('agency_id', $agencyId)->where('status', 'completed')->count(),
            'delivered' => Order::where('agency_id', $agencyId)->where('status', 'delivered')->count()
        ];

        // Services actifs
        $services = Auth::user()->agency?->services()
            ->limit(5)
            ->get();

        // Dernières transactions
        $transactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return Inertia::render('Agency/Dashboard', [
            'stats' => $stats,
            'projectStats' => $projectStats,
            'services' => $services,
            'transactions' => $transactions,
            'user' => $user
        ]);
    }
}
