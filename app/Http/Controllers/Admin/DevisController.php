<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Devis;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DevisController extends Controller
{
    //
    public function index()
    {
        $devis = Devis::with('agency', 'user', 'service')->get();
        return Inertia::render('Admin/Devis', [
            'devis' => $devis
        ]);
    }
}
