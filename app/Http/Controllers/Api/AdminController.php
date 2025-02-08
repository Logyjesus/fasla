<?php

namespace App\Http\Controllers\Api;

use App\Models\Seller;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Resources\SellerResource;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = Seller::role('admin')->paginate(10);
        if (!$admins->isEmpty()) {
            $data = [
                'admins' => SellerResource::collection($admins),
                'pagination' => [
                    'total' => $admins->total(),
                    'current_page' => $admins->currentPage(),
                    'per_page' => $admins->perPage(),
                    'links' => [
                        'first_page' => $admins->url(1),
                        'last_page' => $admins->url($admins->lastPage()),
                    ]
                ]
            ];
            return response()->json($data);
        }
        return response()->json(['message' => 'No admins found'], 404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAdminRequest $request)
    {
        $data = $request->validated();
        $admin = Seller::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' =>  bcrypt($data['password']),
        ]);
        $admin->assignRole('admin');
        return response()->json(new SellerResource($admin), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $admin = Seller::where('slug', $slug)->first();
        if ($admin) {
            return response()->json(new SellerResource($admin));
        }
        return response()->json(['message' => 'Admin not found'], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminRequest $request, string $slug)
    {
        $admin = Seller::where('slug',$slug)->first();
        if ($admin) {
            $data = $request->validated();
            if ($request->has('password')) {
                $data['password'] = bcrypt($data['password']);
            }
            $admin->update($data);
            return response()->json(new SellerResource($admin), 200);
        }
        return response()->json(['message' => 'Admin not found'], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $admin = Seller::where('slug', $slug)->first();
        if ($admin) {
            $admin->delete();
            return response()->json(['message' => 'Admin deleted'], 200);
        }
        return response()->json(['message' => 'Admin not found'], 404);
    }
}