<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Devis;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    //
    public function submitProposal(Request $request,string $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:1',
            'delai_agency' => 'required|integer|min:1',
            'description_agency' => 'nullable|string',
        ]);

        $project = Project::findOrFail($id);

        // Vérifier si l'utilisateur est une agence
        if (Auth::user()->role !== 'agency') {
            return redirect('/projects')->with([
                'message' => 'Seules les agences peuvent proposer des devis.',
                'type' => 'error',
            ]);
        }

        // Vérifier si le projet est encore ouvert
        if ($project->status !== 'open') {
            return redirect('/projects')->with([
                'message' => 'Ce projet n\'est plus disponible.',
                'type' => 'error',
            ]);
        }

        // Vérifier si l'agence a déjà proposé un devis
        $existingDevis = Devis::where('project_id', $project->id)
            ->where('agency_id', Auth::user()->agency->id)
            ->first();

        if ($existingDevis) {
            return redirect('/projects')->with([
                'message' => 'Vous avez déjà proposé un devis pour ce projet.',
                'type' => 'error',
            ]);
        }

        // Créer le devis
        Devis::create([
            'reference' => 'DEV-' . uniqid(),
            'user_id' => $project->user_id,
            'title' => $project->title,
            'service_id' => null,
            'agency_id' => Auth::user()->agency->id,
            'project_id' => $project->id,
            'delai' => $project->delai,
            'delai_agency' => $request->delai_agency,
            'description' => $project->description,
            'description_agency' => $request->description_agency,
            'cdc_file' => $project->cdc_file,
            'budget' => $project->budget,
            'price' => $request->price,
            'status' => 'proposed',
        ]);

        return redirect('/agency/my-devis')->with([
                'message' => 'Votre proposition a été envoyée avec succès.',
                'type' => 'success',
            ]);
    }
}
