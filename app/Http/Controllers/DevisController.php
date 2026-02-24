<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DevisController extends Controller
{
    //
    public function index()
    {
        return Inertia::render('Devis');
    }
}
