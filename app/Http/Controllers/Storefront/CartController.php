<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartItemRequest;
use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * @return array{items: array<int, array{product: Product, quantity: int, subtotal: float}>, total: float}
     */
    private function cartContents(Request $request): array
    {
        $cart = $request->session()->get('cart', []);
        $products = Product::query()
            ->whereKey(array_keys($cart))
            ->get()
            ->keyBy('id');
        $items = [];
        $total = 0.0;

        foreach ($cart as $productId => $quantity) {
            $product = $products->get((int) $productId);

            if (! $product) {
                continue;
            }

            $subtotal = round((float) $product->price * (int) $quantity, 2);
            $total = round($total + $subtotal, 2);
            $items[] = [
                'product' => $product,
                'quantity' => (int) $quantity,
                'subtotal' => $subtotal,
            ];
        }

        return ['items' => $items, 'total' => $total];
    }

    public function index(Request $request): View
    {
        return view('cart.index', $this->cartContents($request));
    }

    public function checkoutForm(Request $request): View|RedirectResponse
    {
        $contents = $this->cartContents($request);

        if ($contents['items'] === []) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Keranjang masih kosong.',
            ]);
        }

        return view('cart.checkout', $contents);
    }

    public function store(CartItemRequest $request, Product $product): RedirectResponse
    {
        $quantity = (int) $request->validated('quantity');
        $cart = $request->session()->get('cart', []);
        $key = (string) $product->getKey();
        $newQuantity = (int) ($cart[$key] ?? 0) + $quantity;

        if ($product->stock < $newQuantity) {
            return back()->withErrors([
                'cart' => 'Jumlah yang diminta melebihi stok '.$product->name.'.',
            ]);
        }

        $cart[$key] = $newQuantity;
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('status', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function update(CartItemRequest $request, Product $product): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        $key = (string) $product->getKey();

        if (! array_key_exists($key, $cart)) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Produk tersebut tidak ada di keranjang.',
            ]);
        }

        $quantity = (int) $request->validated('quantity');

        if ($quantity > $product->stock) {
            return back()->withErrors([
                'cart' => 'Jumlah yang diminta melebihi stok '.$product->name.'.',
            ]);
        }

        $cart[$key] = $quantity;
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('status', 'Jumlah produk di keranjang diperbarui.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[(string) $product->getKey()]);
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('status', 'Produk dihapus dari keranjang.');
    }

    public function checkout(StoreCheckoutRequest $request): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);

        if ($cart === []) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Keranjang masih kosong.',
            ]);
        }

        ksort($cart, SORT_NUMERIC);
        $user = $request->user();
        $shippingAddress = $request->validated('shipping_address');

        $order = DB::transaction(function () use ($cart, $user, $shippingAddress): Order {
            $lines = [];
            $total = 0.0;

            foreach ($cart as $productId => $quantity) {
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail((int) $productId);

                if ($quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'cart' => 'Stok '.$product->name.' tidak cukup. Tersedia '.$product->stock.'.',
                    ]);
                }

                $price = (float) $product->price;
                $total = round($total + ($price * (int) $quantity), 2);
                $lines[] = [
                    'product' => $product,
                    'quantity' => (int) $quantity,
                    'price' => $product->price,
                ];
            }

            $order = $user->orders()->create([
                'total_price' => $total,
                'status' => 'pending',
                'shipping_address' => $shippingAddress,
            ]);

            foreach ($lines as $line) {
                $line['product']->decrement('stock', $line['quantity']);
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'quantity' => $line['quantity'],
                    'price' => $line['price'],
                ]);
            }

            return $order;
        });

        $request->session()->forget('cart');

        return redirect()->route('orders.show', $order);
    }
}
