<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    //
    public function show(Service $service)
    {
        $service = Service::with(['serviceDetails', 'category', 'agency'])
            ->where('slug', $service->slug)
            ->firstOrFail();

        // Récupérer d'autres services similaires (optionnel)
        $similarServices = Service::with('serviceDetails')
            ->where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->limit(3)
            ->get();

        return Inertia::render('Service', [
            'service' => $service,
            'similarServices' => $similarServices,
        ]);
    }
}
