<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    //
    public function index()
    {
        $services = Service::with('agency','category')->latest()->get();
        $agencies = Agency::all();
        return Inertia::render('Home', [
            'agencies' => $agencies,
            'services' => $services
        ]);
    }
}
