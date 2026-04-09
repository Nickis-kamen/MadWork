<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Devis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DevisController extends Controller
{
    //
    public function index()
    {
        $agencyId = Auth::user()->agency->id;

        $devis = Devis::with('user', 'service')->where('agency_id', $agencyId)->get();

        return Inertia::render('Agency/Devis', [
            'devis' => $devis
        ]);
    }

    public function propose(Request $request, Devis $devis)
    {
        $request->validate([
            'description_agency' => 'required|string',
            'delai_agency' => 'required|integer|min:1',
            'price_agency' => 'required|numeric|min:1',
            'status' => 'required|in:proposed'
        ]);

        $devis->update([
            'status' => $request->status,
            'description_agency' => $request->description_agency,
            'delai_agency' => $request->delai_agency,
            'price' => $request->price_agency,
        ]);

        return redirect()->route('agency.devis.index')->with([
            'message' => 'Porposition envoyée avec succès.',
            'type' => 'success'
        ]);
    }
}
