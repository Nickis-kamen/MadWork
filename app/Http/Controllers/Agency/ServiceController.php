<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ServiceController extends Controller
{
    //
    public function index()
    {
        $agency = Auth::user()->agency;

        if (!$agency) {
            return Inertia::render('Agency/Services', [
                'agency' => null,
                'services' => [],
                'categories' => Category::all()
            ]);
        }
        
        $services = Auth::user()->agency->services()->with('category')->latest()->get();
        $categories = Category::all();

        return Inertia::render('Agency/Services',[
            'agency' => $agency,
            'services' => $services,
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|max:2048'
        ]);

        $slug = $this->generateUniqueSlug($request->title);

        if($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/services', 'public');
        }

        $agency_id = Auth::user()->agency->id;

        Service::create([
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'price' => $request->price,
            'agency_id' => $agency_id,
            'category_id' => $request->category_id,
            'image' => $imagePath ?? null
        ]);

        return redirect('/agency/my-services')->with([
            'message' => 'Service créé avec succès !',
            'type' => 'success'
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048'
        ]);

        $slug = $this->generateUniqueSlug($request->title);

        if($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/services', 'public');
        }

        $service->update([
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'image' => $imagePath ?? $service->image
        ]);

        return redirect('/agency/my-services')->with([
            'message' => 'Service mis à jour avec succès !',
            'type' => 'success'
        ]);
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return redirect('/agency/my-services')->with([
            'message' => 'Service supprimé avec succès !',
            'type' => 'success'
        ]);
    }

    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (Service::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
}
