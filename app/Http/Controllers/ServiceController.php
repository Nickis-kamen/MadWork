<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = Service::with(['agency', 'category'])->latest()
            ->whereHas('agency.user.subscriptions', function ($query) {
                $query->where('status', 'active')
                    ->where('ends_at', '>', now());
            });

        // Filtre par recherche
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filtre par agence
        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        }

        // Filtre par catégorie
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Pagination (12 éléments par page)
        $services = $query->paginate(12);

        $agencies = Agency::all();
        $categories = Category::all();

        return Inertia::render('Services', [
            'services' => $services,
            'agencies' => $agencies,
            'categories' => $categories,
            'filters' => $request->only(['search', 'agency_id', 'category_id', 'page']),
        ]);
    }

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

        return Inertia::render('ServiceShow', [
            'service' => $service,
            'similarServices' => $similarServices,
        ]);
    }
}
