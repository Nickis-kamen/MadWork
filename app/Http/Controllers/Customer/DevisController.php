<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Mail\DemandeDevisEmail;
use App\Models\Devis;
use App\Models\Order;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class DevisController extends Controller
{
    //
    public function create(Request $request)
    {
        $services = Service::all();
        $selectedServiceId = $request->query('service_id');
        return Inertia::render('Devis', [
            'services' => $services,
            'selectedServiceId' => $selectedServiceId
        ]);
    }

    public function index()
    {
        $devis = Devis::with('service', 'agency')->where('user_id', Auth::id())->get();

        return Inertia::render('Customer/Devis', [
            'devis' => $devis
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'delai' => 'required|integer|min:1',
            'budget' => 'required|numeric|min:1',
            'cdc_file' => 'nullable|file|mimes:pdf|max:5120', // max 5MB
        ]);

        $userBalance = Auth::user()->balance ? Auth::user()->balance->amount : 0;

        if ($userBalance < 50000) {
            return redirect('/balance')->with([
                'message' => 'Vous devez avoir un solde minimum de 50.000 AR pour soumettre une demande de devis.',
                'type' => 'error'
            ]);
        }

        $service = Service::FindOrFail($request->service_id);

        $path = null;
        if ($request->hasFile('cdc_file')) {
            $file = $request->file('cdc_file');
            $path = $file->store('uploads/cdc_files', 'public');
        }

        Devis::create([
            'user_id' => Auth::id(),
            'reference' => 'DEV-' . uniqid(),
            'service_id' => $request->service_id,
            'agency_id' => $service->agency_id,
            'title' => $request->title,
            'delai' => $request->delai,
            'budget' => $request->budget,
            'description' => $request->description,
            'cdc_file' => $path,
        ]);

        $agencyEmail = $service->agency->user->email;
        
        Mail::to($agencyEmail)->send(new DemandeDevisEmail(Auth::user()));

        return redirect('/my-devis')->with([
            'message' => 'Demande de devis soumise avec succès !',
            'type' => 'success' // ou 'error', 'warning', 'info'
        ]);
    }

    public function accept(Devis $devis)
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
            'devis_id' => $devis->id,
            'description' => 'Acompte devis #' . $devis->reference,
        ]);
        Transaction::create([
            'reference' => 'TXN-' . uniqid(),
            'user_id' => $devis->agency->user->id,
            'type' => 'sale',
            'payment_stage' => 'first',
            'amount' => $base,
            'status' => 'pending',
            'devis_id' => $devis->id,
            'description' => 'Acomptedevis #' . $devis->reference,
        ]);

        $admin = User::where('role', 'admin')->first();

        Transaction::create([
            'reference' => 'TXN-' . uniqid(),
            'user_id' => $admin->id,
            'type' => 'sale',
            'payment_stage' => 'first',
            'amount' => $total,
            'status' => 'pending',
            'devis_id' => $devis->id,
            'description' => 'Acomptedevis #' . $devis->reference,
        ]);

        return redirect('/payment/' . $transaction->reference)->with([
            'message' => 'Devis accepté avec succès !',
            'type' => 'success', // ou 'error', 'warning', 'info'
        ]);
    }
}
