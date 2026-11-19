<?php

namespace App\Http\Controllers;
use App\Models\Order;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
public function index()
{
    $orders = Order::with(['user', 'items'])
        ->orderByDesc('order_date')
        ->orderByDesc('order_id')
        ->get();

    $orders->each(function ($order) {
        $order->positionsCount = $order->items->count();
        $order->totalSum = (float) $order->items->sum(function ($item) {
            return $item->quantity * (float) $item->unit_price;
        });
    });

    return view('home', compact('orders'));
}
}
