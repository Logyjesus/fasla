<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\sellerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubCategoryController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('orders', OrderController::class)->except(['index','update']);
    Route::get('/my-orders',[OrderController::class, 'myOrders']);
    Route::delete('/orders/{slug}', [OrderController::class, 'destroy']);
    Route::get('/orders/{slug}', [OrderController::class, 'show']);
    Route::get('/products/{slug}',[ProductController::class,'getProductsBySubCategory']);
    Route::get('/product/{slug}',[ProductController::class,'show']);
    Route::get('/categories',[CategoryController::class,'index']);
    Route::get('/sub-categories/{slug}',[SubCategoryController::class,'getSubCategoriesByCategory']);
});

Route::middleware('auth:seller')->prefix('dashboard')->group( function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout',[AuthController::class,'DashboardLogout']);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('orders', OrderController::class)->only(['index','update']);
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('sub-categories', SubCategoryController::class);
        Route::get('/sub-categories/{slug}',[SubCategoryController::class,'getSubCategoriesByCategory']);
        Route::apiResource('users', UserController::class);
        Route::apiResource('sellers',sellerController::class);
        Route::apiResource('admins',AdminController::class);
        Route::delete('/orders/{slug}', [OrderController::class, 'destroy']);
        Route::get('/orders/{slug}', [OrderController::class, 'show']);
    });
});
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('dashboard/login', [AuthController::class, 'dashboardLogin']);


Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated'], 401);
})->name('login');
