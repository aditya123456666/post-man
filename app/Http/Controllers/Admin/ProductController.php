<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $products = Product::query()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'productCount' => Product::count(),
            'orderCount' => Order::count(),
            'lowStockCount' => Product::where('stock', '<=', 5)->count(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product,
            'isEditing' => false,
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $attributes = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $attributes['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($attributes);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', [
            'product' => $product,
            'isEditing' => true,
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $attributes = $request->safe()->except('image');
        $oldImage = $product->image;

        if ($request->hasFile('image')) {
            $attributes['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($attributes);

        if ($request->hasFile('image') && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        /** @var array{deleted: bool, image: ?string} $deletion */
        $deletion = DB::transaction(function () use ($product): array {
            $lockedProduct = Product::query()
                ->lockForUpdate()
                ->findOrFail($product->getKey());

            if ($lockedProduct->orderItems()->exists()) {
                return ['deleted' => false, 'image' => null];
            }

            $image = $lockedProduct->image;
            $lockedProduct->delete();

            return ['deleted' => true, 'image' => $image];
        });

        if (! $deletion['deleted']) {
            return back()->withErrors([
                'product' => 'Produk yang sudah tercatat dalam pesanan tidak dapat dihapus.',
            ]);
        }

        if ($deletion['image']) {
            Storage::disk('public')->delete($deletion['image']);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produk berhasil dihapus.');
    }
}
