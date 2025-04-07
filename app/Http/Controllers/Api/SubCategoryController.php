<?php

namespace App\Http\Controllers\Api;

use App\Models\SubCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubCategoryRequest;
use App\Http\Resources\SubCategoryResource;
use App\Models\Category;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subcategories = SubCategory::all();
        if(count($subcategories) > 0)
        {
            return response()->json(SubCategoryResource::collection($subcategories));
        }
        return response()->json(['message' => 'No subcategories found'], 404);
    }

    public function getSubCategoriesByCategory(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $subcategories = SubCategory::where('category_id', $category->id)->get();
        if(count($subcategories) > 0)
        {
            return response()->json(SubCategoryResource::collection($subcategories));
        }
        return response()->json(['message' => 'No subcategories found'], 404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubCategoryRequest $request)
    {
        $data = $request->validated();
        $subcategory = SubCategory::create([
            'name' => $data['name'],
            'category_id' => $data['category_id'],
        ]);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move('images', $filename);
            $subcategory->image = $filename;
            $subcategory->save();
        }
        return response()->json(new SubCategoryResource($subcategory), 201);
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubCategoryRequest $request, string $slug)
    {
        $data = $request->validated();
        $subcategory = SubCategory::where('slug',$slug)->first();
        if($subcategory)
        {
            $subcategory->update($data);
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() .'.' . $image->getClientOriginalExtension();
                $image->move('images', $filename);
                $subcategory->image = $filename;
                $subcategory->save();
            }
            return response()->json(new SubCategoryResource($subcategory), 200);
        }
        return response()->json(['message' => 'Subcategory not found'], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $subcategory = SubCategory::where('slug',$slug)->first();
        if($subcategory)
        {
            $subcategory->delete();
            return response()->json(['message' => 'Subcategory deleted successfully'], 200);
        }
        return response()->json(['message' => 'Subcategory not found'], 404);
    }
}
