<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query()
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

        return CategoryResource::collection($categories);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create(
            $request->validated()
        );

        return new CategoryResource($category);
    }

    public function show(Category $category)
    {
        $category->load('products');
        return new CategoryResource($category);
    }
    public function products(Request $request, Category $category)
    {
        $products = $category->products()
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

    public function update(
        UpdateCategoryRequest $request,
        Category $category
    ) {
        $category->update(
            $request->validated()
        );

        return new CategoryResource(
            $category->fresh()
        );
    }

    public function destroy(Category $category): Response
    {
        $category->delete();

        return response()->noContent();
    }
}