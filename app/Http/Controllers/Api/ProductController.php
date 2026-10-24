<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(ProductIndexRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $sort = $filters['sort'] ?? 'created_at';
        $direction = $filters['direction'] ?? 'desc';

        $products = Product::query()
            ->when($filters['search'] ?? null, fn ($query, string $search) => $query->where('name', 'like', '%'.$search.'%'))
            ->when(isset($filters['min_price']), fn ($query) => $query->where('price', '>=', $filters['min_price']))
            ->when(isset($filters['max_price']), fn ($query) => $query->where('price', '<=', $filters['max_price']))
            ->when(
                array_key_exists('in_stock', $filters),
                fn ($query) => $query->where('stock', $filters['in_stock'] ? '>' : '=', 0),
            )
            ->orderBy($sort, $direction)
            ->orderBy('id', $direction)
            ->paginate($filters['per_page'] ?? 12)
            ->withQueryString();

        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $attributes = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $attributes['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($attributes);

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
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

        return new ProductResource($product->refresh());
    }

    public function destroy(Product $product): JsonResponse|Response
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
            return response()->json([
                'message' => 'Produk yang sudah tercatat dalam pesanan tidak dapat dihapus.',
            ], 409);
        }

        if ($deletion['image']) {
            Storage::disk('public')->delete($deletion['image']);
        }

        return response()->noContent();
    }
}
