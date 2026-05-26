<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    //
    public function index()
    {
        $subscriptions = Subscription::with('agency')->latest()->get();
        
        return Inertia::render('Admin/Subscription', [
            //
            'subscriptions' => $subscriptions
        ]);
    }
}
