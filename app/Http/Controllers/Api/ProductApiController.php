<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * Daftar semua produk aktif (untuk tampilan kasir Flutter).
     *
     * Query params:
     * - search: string (cari berdasarkan nama/barcode)
     * - category_id: int (filter berdasarkan kategori)
     */
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->active()
            ->orderBy('name');

        // Filter pencarian
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->get();

        return ProductResource::collection($products);
    }

    /**
     * Detail satu produk.
     */
    public function show(Product $product)
    {
        $product->load('category');

        return new ProductResource($product);
    }

    /**
     * Daftar semua kategori.
     */
    public function categories()
    {
        $categories = Category::withCount(['products' => function ($q) {
            $q->active();
        }])
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'product_count' => $category->products_count,
                ];
            });

        return response()->json([
            'data' => $categories,
        ]);
    }
}
