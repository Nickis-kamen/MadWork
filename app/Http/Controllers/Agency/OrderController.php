<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    //
    public function index()
    {
        $agencyId = Auth::user()->agency->id;
        $orders = Order::with('service', 'client', 'devis')->where('agency_id', $agencyId)->get();

        return Inertia::render('Agency/Order', [
            'orders' => $orders
        ]);
    }

    public function updateProgress(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'progress' => 'required|integer|min:0|max:100',
            'progress_note' => 'nullable|string|max:1000'
        ]);
        $progress = $request->progress;

        $agencyId = Auth::user()->agency->id;

        $order = Order::where('agency_id', $agencyId)
            ->where('id', $id)
            ->firstOrFail();
            
        if($request->status === 'completed')
        {
            $progress = 100;
        }

        $order->update([
            'status' => $request->status,
            'progress' => $progress,
            'progress_note' => $request->progress_note
        ]);

        return redirect()->route('agency.orders.index')->with([
            'message' => 'Progression mise à jour avec succès',
            'type' => 'success'
        ]);
    }

    public function delivered(string $id)
    {
        $agencyId = Auth::user()->agency->id;
        $order = Order::where('agency_id', $agencyId)
            ->where('id', $id)
            ->firstOrFail();

        $order->update([
            'status' => 'delivered'
        ]);

        return redirect()->route('agency.orders.index')->with([
            'message' => 'Commande marquée comme livrée avec succès',
            'type' => 'success'
        ]);
    }

}
