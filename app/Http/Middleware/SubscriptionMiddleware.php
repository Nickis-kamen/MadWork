<?php

namespace App\Http\Middleware;

use App\Models\Subscription;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check())
        {
            if(Auth::user()->role === 'agency')
            {
                $hasActiveSubscription = Subscription::where('agency_id', Auth::user()->id)
                ->where('status', 'active')
                ->where('ends_at', '>', Carbon::now())
                ->exists();

            // Si pas d'abonnement actif, rediriger
                if (!$hasActiveSubscription) {
                    return redirect('/agency/subscription')->with([
                        'message' => 'Vous devez avoir un abonnement actif pour accéder à cette page',
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
