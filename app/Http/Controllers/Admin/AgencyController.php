<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AgencyController extends Controller
{
    //
    public function index()
    {
        //
        $agencies = Agency::with('user')->get();

        return Inertia::render('Admin/Agencies', [
            'agencies' => $agencies
        ]);
    }
}
