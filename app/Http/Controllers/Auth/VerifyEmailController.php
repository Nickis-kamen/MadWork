<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }

    public function verifyEmail(string $token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
                return redirect('/login')->with([
                'message' => 'Token de vérification invalide.',
                'type' => 'error'
            ]);
        }

        $user->is_verified = true;
        $user->email_verified_at = now();
        $user->verification_token = null;
        $user->save();

        return redirect('/login')->with([
            'message' => 'Votre adresse e-mail a été vérifiée.',
            'type' => 'success'
        ]);

    }
}
