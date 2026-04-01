<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AgencyController extends Controller
{
    //
    public function index()
    {
        $agency = Auth::user()->agency;
        return Inertia::render('Agency/Agency', [
            'agency' => $agency
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:agencies,email',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'address' => 'required|string|max:255',
        ]);

        $slug = $this->generateUniqueSlug($request->name);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('uploads/agencies', 'public');
        }else {
            $path = null;
        }
        Agency::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'slug' => $slug,
            'email' => $request->email,
            'description' => $request->description,
            'image' => $path,
            'address' => $request->address,
        ]);

        return redirect('/agency/my-agency')->with([
            'message' => 'Agence créée avec succès !',
            'type' => 'success' // ou 'error', 'warning', 'info'
        ]);
    }

    public function update(Agency $agency, Request $request)
    {
        $agency = Auth::user()->agency;
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:agencies,email,'.$agency->id,
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'address' => 'required|string|max:255',
        ]);

        $slug = $this->generateUniqueSlug($request->name);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('uploads/agencies', 'public');
        }else {
            $path = $agency->image;
        }

        $agency->update([
            'name' => $request->name,
            'slug' => $slug,
            'email' => $request->email,
            'description' => $request->description,
            'image' => $path,
            'address' => $request->address,
        ]);

        return redirect('/agency/my-agency')->with([
            'message' => 'Agence mise à jour avec succès !',
            'type' => 'success'
        ]);
    }

    public function destroy(Agency $agency)
    {
        $agency->delete();

        return redirect('/agency/my-agency')->with([
            'message' => 'Agence supprimée avec succès',
            'type' => 'success'
        ]);
    }

    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (Agency::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
}
