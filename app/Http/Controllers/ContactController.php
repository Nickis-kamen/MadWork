<?php

namespace App\Http\Controllers;

use App\Mail\ContactEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    //
    public function send(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'entreprise' => 'nullable|string|max:255',
            'userMessage' => 'required|string',
        ]);

        // Envoyer l'e-mail
        Mail::to(config('mail.contact_address'))->send(new ContactEmail(
            $request->name,
            $request->email,
            $request->phone,
            $request->entreprise,
            $request->userMessage
        ));

        return redirect()->back()->with([
            'message' => 'Votre message a été envoyé avec succès. Nous vous contacterons bientôt.',
            'type' => 'success'
        ]);
    }
}
