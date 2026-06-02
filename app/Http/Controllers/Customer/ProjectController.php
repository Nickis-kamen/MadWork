<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectController extends Controller
{
    //
    public function index()
    {
        $projects = Project::with('category')->where('user_id', Auth::id())->get();

        return Inertia::render('Customer/Projects', [
            'projects' => $projects
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'delai' => 'required|integer|min:1',
            'description' => 'required|string',
            'cdc_file' => 'nullable|file|mimes:pdf|max:5120', // max 5MB
            'budget' => 'required|numeric|min:1',
        ]);

        $userBalance = Auth::user()->balance ? Auth::user()->balance->amount : 0;

        if ($userBalance < 50000) {
            return redirect('/balance')->with([
                'message' => 'Vous devez avoir un solde minimum de 50.000 AR pour publier un projet.',
                'type' => 'error'
            ]);
        }

        $path = null;
        if ($request->hasFile('cdc_file')) {
            $file = $request->file('cdc_file');
            $path = $file->store('uploads/cdc_files', 'public');
        }

        Project::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'category_id' => $request->category_id,
            'delai' => $request->delai,
            'description' => $request->description,
            'cdc_file' => $path,
            'budget' => $request->budget,
        ]);

        return redirect('/customer/projects')->with([
            'message' => 'Projet publié avec succès.',
            'type' => 'success'
        ]);
    }

    public function edit(Project $project)
    {
        $project->load('category');
        $categories = Category::all();

        return Inertia::render('Customer/EditProject', [
            'project' => $project,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'delai' => 'required|integer|min:1',
            'description' => 'required|string',
            'cdc_file' => 'nullable|file|mimes:pdf|max:5120', // max 5MB
            'budget' => 'required|numeric|min:1',
        ]);

        $path = $project->cdc_file;
        if ($request->hasFile('cdc_file')) {
            $file = $request->file('cdc_file');
            $path = $file->store('uploads/cdc_files', 'public');
        }

        $project->update([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'delai' => $request->delai,
            'description' => $request->description,
            'cdc_file' => $path,
            'budget' => $request->budget,
        ]);

        return redirect('/customer/projects')->with([
            'message' => 'Projet mis à jour avec succès.',
            'type' => 'success'
        ]);
    }

    public function closed(Project $project)
    {
        $project->update([
            'status' => 'closed'
        ]);

        return redirect('/customer/projects')->with([
            'message' => 'Projet fermé avec succès.',
            'type' => 'success'
        ]);
    }
}
