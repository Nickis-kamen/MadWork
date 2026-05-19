<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AgencyController extends Controller
{
    //
    public function show(Agency $agency)
    {
        $agency->load('services');
        $count_order = $agency->orders()->whereIn('status', ['completed', 'delivered'])->count();
        
        return Inertia::render('AgencyShow', [
            'agency' => $agency,
            'count_order' => $count_order,
        ]);
    }
}
