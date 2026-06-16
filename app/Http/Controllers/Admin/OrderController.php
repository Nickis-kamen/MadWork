<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    //
    public function index()
    {
        $orders = Order::with('agency', 'client', 'service')->get();

        return Inertia::render('Admin/Orders', [
            'orders' => $orders
        ]);
    }
}
