<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Devis;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['user', 'category']);

        // Filtre par recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtre par catégorie
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $projects = $query->paginate(9);
        $categories = Category::all();

        return Inertia::render('Projects', [
            'projects' => $projects,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category_id'])
        ]);
    }

    public function show(Project $project)
    {
        $project->load('user', 'category');

        // Vérifier si l'agence a déjà proposé un devis pour ce projet
        $existingQuotation = null;
        if (Auth::check() && Auth::user()->role === 'agency') {
            $existingQuotation = Devis::where('project_id', $project->id)
                ->where('agency_id', Auth::user()->agency->id)
                ->first();
        }
        return Inertia::render('ProjectShow', [
            'project' => $project,
            'existingQuotation' => $existingQuotation
        ]);
    }
}
