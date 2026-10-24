<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $items = collect($validated['items'])
            ->sortBy('product_id')
            ->values()
            ->all();

        $order = DB::transaction(function () use ($items, $validated, $user): Order {
            $total = 0;
            $orderItems = [];

            foreach ($items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    abort(422, 'Stok produk '.$product->name.' tidak mencukupi.');
                }

                $subtotal = round((float) $product->price * $item['quantity'], 2);
                $total = round($total + $subtotal, 2);

                $product->decrement('stock', $item['quantity']);

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ];
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $total,
                'status' => 'pending',
                'shipping_address' => $validated['shipping_address'],
            ]);

            foreach ($orderItems as $orderItem) {
                $order->items()->create($orderItem);
            }

            return $order->load('items.product');
        });

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    public function history(Request $request): AnonymousResourceCollection
    {
        $orders = $request->user()
            ->orders()
            ->with('items.product')
            ->orderByDesc('created_at')
            ->paginate(10);

        return OrderResource::collection($orders);
    }
}
