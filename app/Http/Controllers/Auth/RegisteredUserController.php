<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        $categories = Category::all();
        return Inertia::render('Auth/Register', [
            'categories' => $categories
        ]);
    }
    public function createAgency(): Response
    {
        return Inertia::render('Auth/RegisterAgency');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'image' => [ 'nullable','image', 'max:5120'],
            'phone' => 'required|string|max:20',
            'role' => 'required|string|in:customer,agency',
            'address' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('uploads/users', 'public');
        }else {
            $path = null;
        }

        $verification_token = Str::random(60);

        $user = User::create([
            'name' => $request->name,
            'first_name' => $request->first_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_token' => $verification_token,
            'image' => $path,
            'phone' => $request->phone,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'role' => $request->role,
            'category_id' => $request->category_id,
        ]);

        event(new Registered($user));

        Mail::to($user->email)->send(new VerifyEmail($user, $verification_token));

        return redirect('/login')->with([
            'message' => 'Veuillez vérifier votre adresse e-mail pour activer votre compte',
            'type' => 'info'
        ]);
    }
}
