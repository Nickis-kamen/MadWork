<?php

namespace App\Http\Middleware;

use App\Models\Subscription;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NoSubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hasActiveSubscription = Subscription::where('agency_id', Auth::user()->id)
                    ->where('status', 'active')
                    ->where('ends_at', '>', Carbon::now())
                    ->exists();

        if(Auth::check())
        {
            if(Auth::user()->role === 'agency')
            {
                if(Auth::user()->subscriptions && $hasActiveSubscription)
                {
                    // Si abonnement actif, rediriger
                    return redirect('/agency/subscriptions')->with([
                                'message' => 'Vous avez déjà un abonnement actif',
                                'type' => 'error'
                            ]);
                }

                return $next($request);
            }else{
                return redirect('/');
            }
        }else{
            return redirect('/login');
        }
    }
}
