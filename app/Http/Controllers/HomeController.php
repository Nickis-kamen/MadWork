<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Category;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    //
    public function index()
    {
        if (Auth::check()) {
            $userCategoryId = Auth::user()->category_id; // Fetch the user's category ID

            // Fetch services with active subscriptions and prioritize those in the user's category
            $services = Service::with(['agency', 'category'])
                ->whereHas('agency.user.subscriptions', function ($query) {
                    $query->where('status', 'active')
                        ->where('ends_at', '>', now());
                })
                ->orderByRaw("CASE WHEN category_id = ? THEN 0 ELSE 1 END", [$userCategoryId])
                ->latest()
                ->limit(3)
                ->get();

        } else {

            // Fetch services with active subscriptions
            $services = Service::with(['agency', 'category'])
                ->whereHas('agency.user.subscriptions', function ($query) {
                    $query->where('status', 'active')
                        ->where('ends_at', '>', now());
                })
                ->limit(3)
                ->latest()
                ->get();
        }

        // Fetch agencies with active subscriptions
        $agencies = Agency::whereHas('user.subscriptions', function ($query) {
            $query->where('status', 'active')
                ->where('ends_at', '>', now());
        })->limit(3)->get();

        $categories = Category::all();
        $projects = Project::with('user', 'category')->limit(3)->latest()->get();

        return Inertia::render('Home', [
            'agencies' => $agencies,
            'services' => $services,
            'categories' => $categories,
            'projects' => $projects
        ]);
    }
}
