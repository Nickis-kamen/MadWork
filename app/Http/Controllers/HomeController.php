<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    //
    public function index()
    {
        $agencies = Agency::all();
        return Inertia::render('Home', [
            'agencies' => $agencies
        ]);
    }
}
