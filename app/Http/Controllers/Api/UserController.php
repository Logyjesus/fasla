<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(10);
        if (!$users->isEmpty()) {
            $data = [
                'users' => UserResource::collection($users),
                'pagination' => [
                    'total' => $users->total(),
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'links' => [
                        'first_page' => $users->url(1),
                        'last_page' => $users->url($users->lastPage()),
                    ]
                ]
            ];
            return response()->json($data);
        }
        return response()->json(['message' => 'No users found'], 404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterRequest $request)
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' =>  bcrypt($data['password']),
        ]);
        return response()->json(new UserResource($user), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $user = User::where('slug',$slug)->first();
        if ($user) {
            return response()->json(new UserResource($user));
            }
            return response()->json(['message' => 'User not found'], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $slug)
    {
        $user = User::where('slug',$slug)->first();
        if ($user) {
            $data = $request->validated();
            if ($request->has('password')) {
                $data['password'] = bcrypt($data['password']);
            }
            $user->update($data);
            return response()->json(new UserResource($user), 200);
        }
        return response()->json(['message' => 'User not found'], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $user = User::where('slug',$slug)->first();
        if ($user) {
            $user->delete();
            return response()->json(['message' => 'User deleted'], 200);
        }
        return response()->json(['message' => 'User not found'], 404);
    }
}