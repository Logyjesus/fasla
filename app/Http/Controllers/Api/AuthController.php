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

    public function dashboardLogin(LoginRequest $request)
    {
        $data = $request->validated();
        if(Auth::guard('admin')->attempt(['email' => $data['email'],'password' => $data['password']]))
        {
            $admin = Auth::guard('admin')->user();
            $token = $admin->createToken('admin')->plainTextToken;
            $guard = 'admin';
            return response(compact('admin', 'token','guard'), 200);
        }
        else if (Auth::guard('seller')->attempt(['email' => $data['email'],'password' => $data['password']]))
        {
            $seller = Auth::guard('seller')->user();
            $token = $seller->createToken('seller')->plainTextToken;
            $guard = 'seller';
            return response(compact('seller', 'token','guard'), 200);
        }
        return response(['message' => 'Provided email address or password is incorrect'], 422);
    }

    public function logout(Request $request)
    {
        if (Auth::guard('web')->check())
        {
            $user = Auth::guard('web')->user();
            $user->tokens()->delete();
        }
        elseif (Auth::guard('seller')->check())
        {
            $seller = Auth::guard('seller')->user();
            $seller->tokens()->delete();
        }
        elseif (Auth::guard('admin')->check())
        {
            $admin = Auth::guard('admin')->user();
            $admin->tokens()->delete();
        }
        return response('', 204);
    }
}