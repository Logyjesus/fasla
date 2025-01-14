<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;

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
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response('', 204);
    }

}