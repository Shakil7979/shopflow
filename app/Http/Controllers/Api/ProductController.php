<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {
    }

    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(
                    'name',
                    'like',
                    '%' . $request->search . '%'
                );
            })
            ->when($request->has('is_active'), function ($query) use ($request) {
                $query->where(
                    'is_active',
                    $request->boolean('is_active')
                );
            })
            ->latest()
            ->paginate(
                $request->integer('per_page', 15)
            );

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create(
            $request->validated()
        );

        return new ProductResource($product);
    }

    public function show(Product $product)
    {
        $product->load('category');
        return new ProductResource($product);
    }

    public function update(
        UpdateProductRequest $request,
        Product $product
    ) {
        $product = $this->productService->update(
            $product,
            $request->validated()
        );

        return new ProductResource($product);
    }

    public function destroy(Product $product): Response
    {
        $this->productService->delete($product);

        return response()->noContent();
    }
}