<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\sellerController;

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
    Route::post('/logout',[AuthController::class,'DashboardLogout']);

    Route::middleware('role:admin')->group(function () {
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('sub-categories', SubCategoryController::class);
        Route::apiResource('products', ProductController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('sellers',sellerController::class);
    });
});
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('dashboard/login', [AuthController::class, 'dashboardLogin']);


Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated'], 401);
})->name('login');