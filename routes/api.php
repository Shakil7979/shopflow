<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']); 
 
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin-test', function (Request $request) {
        return response()->json([
            'message' => 'Welcome Admin!',
            'user' => $request->user(),
        ]);
    });
});

Route::middleware(['auth:sanctum', 'role:vendor'])->group(function () {
    Route::get('/vendor-test', function (Request $request) {
        return response()->json([
            'message' => 'Welcome Vendor!',
            'user' => $request->user(),
        ]);
    });
});

Route::apiResource('products', ProductController::class);
Route::apiResource('categories', CategoryController::class);
