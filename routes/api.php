<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CategoryController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

});

Route::middleware('auth:seller')->prefix('dashboard')->group( function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::apiResource('categories', CategoryController::class);
    Route::post('/logout',[AuthController::class,'DashboardLogout']);
});
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('dashboard/login', [AuthController::class, 'dashboardLogin']);