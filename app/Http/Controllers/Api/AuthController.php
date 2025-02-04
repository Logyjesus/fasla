<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Seller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\SellerResource;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' =>  bcrypt($data['password']),
        ]);
        $token = $user->createToken('main')->plainTextToken;
        return response(compact('user','token'),201);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = Auth::user();
            $token = $user->createToken('main')->plainTextToken;
            return response(compact('user', 'token'), 200);
        }
        return response(['message' => 'Provided email address or password is incorrect'], 422);
    }

    public function dashboardLogin(LoginRequest $request)
    {
        $data = $request->validated();
        $seller = Seller::where('email', $data['email'])->first();
        if (!$seller || !Hash::check($data['password'], $seller->password)) {
            return response()->json(['message' => 'Provided email address or password is incorrect'], 401);
        }
        $token = $seller->createToken('seller')->plainTextToken;
        return response()->json(['seller' =>new SellerResource($seller),'token' => $token], 200);
    }

    public function logout(Request $request)
    {
            $user = Auth::user();
            $user->tokens()->delete();
            return response('', 204);
    }

    public function dashboardLogout(Request $request)
    {
        $seller = Auth::guard('seller')->user();
        $seller->tokens()->delete();
        return response('', 204);
    }
}
