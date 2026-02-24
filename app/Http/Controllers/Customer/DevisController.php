<?php

namespace App\Http\Controllers\Customer;

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
        $devis = Devis::where('user_id', Auth::id())->get();

        return Inertia::render('Customer/Devis', [
            'devis' => $devis
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'delai' => 'required|integer|min:1',
            'cdc_file' => 'nullable|file|mimes:pdf|max:5120', // max 5MB
        ]);

        $path = null;
        if ($request->hasFile('cdc_file')) {
            $file = $request->file('cdc_file');
            $path = $file->store('uploads/cdc_files', 'public');
        }

        Devis::create([
            'user_id' => Auth::id(),
            'service_type' => $validated['service_type'],
            'title' => $validated['title'],
            'delai' => $validated['delai'],
            'description' => $validated['description'],
            'cdc_file' => $path,
        ]);

        return redirect('/devis')->with([
            'message' => 'Demande de devis soumise avec succès !',
            'type' => 'success' // ou 'error', 'warning', 'info'
        ]);
    }
}
