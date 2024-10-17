<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')->get();
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required'],
            'price'       => ['required', 'numeric'],
            'category_id' => ['required', 'numeric', 'exists:categories,id'],
        ]);

        $product = Product::query()->create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'category_id' => $request->category_id
        ]);

        return response()->json([
            'message' => 'Product successful created',
            'data'    => new ProductResource($product)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'price'       => ['nullable', 'numeric'],
            'category_id' => ['nullable', 'numeric', 'exists:categories,id'],
        ]);

        $product = Product::query()->find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        $product->update([
            'name'        => $request->name ?? $product->name,
            'description' => $request->description ?? $product->description,
            'price'       => $request->price ?? $product->price,
            'category_id' => $request->category_id ?? $product->category_id,
        ]);

        return response()->json([
            'message' => 'Product successful updated',
            'data'    => new ProductResource($product)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::query()->find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        $product->delete();
        return response()->json([
            'message' => 'Product successful deleted'
        ]);
    }
}
