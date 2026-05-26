<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Product;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Product::with('category')->get(), 200);
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        return response()->json($product, 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product->load('category'), 200);
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());
        return response()->json($product, 200);
    }


    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(['message' => 'Товар успешно удален'], 204);
    }
}
