<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sellerID = Auth::guard('seller')->user()->id;
        $products = Product::where('seller_id', $sellerID)->latest()->paginate(10);

        if (!$products->isEmpty()) {
            $data = [
                'products' => ProductResource::collection($products),
                'pagination' => [
                    'total' => $products->total(),
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'links' => [
                        'first_page' => $products->url(1),
                        'last_page' => $products->url($products->lastPage()),
                    ]
                ]
            ];
            return response()->json($data);
        }

        return response()->json(['message' => 'No products found'], 404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $sellerID = Auth::guard('seller')->user()->id;

        $data = $request->validated();
        $data['seller_id'] = $sellerID;

        $product = Product::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'quantity' => $data['quantity'],
            'seller_id' => $data['seller_id'],
            'sub_category_id' => $data['sub_category_id'],
            'discounted_price' => $data['discounted_price'],
        ]);

        if (!empty($data['colors'])) {
            foreach ($data['colors'] as $color) {
                $product->colors()->create(['color_name' => $color]);
            }
        }

        if (!empty($data['sizes'])) {
            foreach ($data['sizes'] as $size) {
                $product->sizes()->create(['size' => $size]);
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        return response()->json(new ProductResource($product), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json(new ProductResource($product));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, $slug)
{
    $product = Product::where('slug', $slug)->firstOrFail();

    $sellerID = Auth::guard('seller')->user()->id;

    $data = $request->validated();
    $data['seller_id'] = $sellerID;

    $data['colors'] = isset($data['colors'][0]) ? json_decode($data['colors'][0], true) : [];
    $data['sizes'] = isset($data['sizes'][0]) ? json_decode($data['sizes'][0], true) : [];

    $product->update([
        'name' => $data['name'],
        'description' => $data['description'],
        'price' => $data['price'],
        'quantity' => $data['quantity'],
        'seller_id' => $data['seller_id'],
        'sub_category_id' => $data['sub_category_id'],
        'discounted_price' => $data['discounted_price'],
    ]);

    if (!empty($data['colors'])) {
        $product->colors()->delete();
        foreach ($data['colors'] as $color) {
            $product->colors()->create(['color_name' => $color]);
        }
    }

    if (!empty($data['sizes'])) {
        $product->sizes()->delete();
        foreach ($data['sizes'] as $size) {
            $product->sizes()->create(['size' => $size]);
        }
    }

    if ($request->hasFile('images')) {
        $product->images()->delete();
        foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');
            $product->images()->create(['image_path' => $path]);
        }
    }

    return response()->json(new ProductResource($product), 200);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        if($product) {
            $product->delete();
            return response()->json(['message' => 'Product deleted'], 200);
        }
        return response()->json(['message' => 'Product not found'], 404);
    }
}
