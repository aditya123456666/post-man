<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function show(Request $request, Order $order): View
    {
        $order = $request->user()
            ->orders()
            ->with('items.product')
            ->findOrFail($order->id);

        return view('orders.show', compact('order'));
    }
}
