<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        if (count($categories) > 0) {
            return response()->json(CategoryResource::collection($categories),200);
        }
        return response()->json(['message' => 'No categories found'], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $category = Category::create([
            'name' => $data['name'],
        ]);
        return response()->json(new CategoryResource($category), 201);
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     $category = Category::find($id);
    //     if ($category) {
    //         return response()->json(new CategoryResource($category), 200);
    //     }
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, string $slug)
    {
        $data = $request->validated();
        $category = Category::find($slug);
        if ($category) {
            $category->update([
                'name' => $data['name'],
            ]);
            return response()->json(new CategoryResource($category), 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $category = Category::find($slug);
        if ($category) {
            $category->delete();
            return response()->json(['message' => 'Category deleted'], 200);
        }
    }
}
