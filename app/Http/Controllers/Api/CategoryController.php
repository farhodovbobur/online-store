<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection(Category::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:' . Category::class],
        ]);

        $category = Category::query()->create([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => "Category successful created",
            'data'    => new CategoryResource($category)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): CategoryResource|JsonResponse
    {
        $category = Category::query()->find($id);

        return $category ? new CategoryResource($category) :
            response()->json([
                'message' => "Category not found",
            ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'name' => ['unique:' . Category::class],
        ]);

        $category = Category::query()->find($id);

        if (!$category) {
            return response()->json([
                'message' => "Category not found",
            ], 404);
        }

        $category->update([
            'name'        => $request->name ? $request->name : $category->name,
            'description' => $request->description ?? $category->description,
            'parent'      => $request->parent ?? $category->parent,
        ]);

        return response()->json([
            'message' => "Category successful updated",
            'data'    => new CategoryResource($category)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $category = Category::query()->find($id);

        if (!$category) {

            return response()->json([
                'message' => "Category not found"
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => "Category successful deleted",
        ]);
    }
}
