<?php

namespace App\Http\Controllers\Api;

use App\Models\Seller;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSellerRequest;
use App\Http\Requests\UpdateSellerRequest;
use App\Http\Resources\SellerResource;

class sellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sellers = Seller::role('seller')->paginate(10);
        if (!$sellers->isEmpty()) {
            $data = [
                'sellers' => SellerResource::collection($sellers),
                'pagination' => [
                    'total' => $sellers->total(),
                    'current_page' => $sellers->currentPage(),
                    'per_page' => $sellers->perPage(),
                    'links' => [
                        'first_page' => $sellers->url(1),
                        'last_page' => $sellers->url($sellers->lastPage()),
                    ]
                ]
            ];
            return response()->json($data);
        }
        return response()->json(['message' => 'No sellers found'], 404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSellerRequest $request)
    {
        $data = $request->validated();
        $seller = Seller::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'store_name' => $data['store_name'],
            'address' => $data['address'],
            'password' =>  bcrypt($data['password']),
        ]);
        $seller->assignRole('seller');
        return response()->json(new SellerResource($seller), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $seller = Seller::where('slug', $slug)->first();
        if ($seller) {
            return response()->json(new SellerResource($seller));
        }
        return response()->json(['message' => 'Seller not found'], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSellerRequest $request, string $slug)
    {
        $seller = Seller::where('slug',$slug)->first();
        if ($seller) {
            $data = $request->validated();
            if ($request->has('password')) {
                $data['password'] = bcrypt($data['password']);
            }
            $seller->update($data);
            return response()->json(new SellerResource($seller), 200);
        }
        return response()->json(['message' => 'seller not found'], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $seller = Seller::where('slug', $slug)->first();
        if ($seller) {
            $seller->delete();
            return response()->json(['message' => 'Seller deleted successfully'], 200);
        }
        return response()->json(['message' => 'Seller not found'], 404);
    }
}
