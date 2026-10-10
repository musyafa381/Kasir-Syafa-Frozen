<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CheckoutApiController;
use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — untuk Flutter POS App
|--------------------------------------------------------------------------
|
| Semua route di sini otomatis mendapat prefix /api.
| Contoh: Route::get('/products') → GET /api/products
|
*/

// ──────────────────────────────────────
// Public routes (tanpa auth)
// ──────────────────────────────────────
Route::post('/login', [AuthApiController::class, 'login']);

// ──────────────────────────────────────
// Protected routes (butuh token Sanctum)
// ──────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/user', [AuthApiController::class, 'me']);

    // Products
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/products/{product}', [ProductApiController::class, 'show']);
    Route::get('/categories', [ProductApiController::class, 'categories']);

    // Checkout
    Route::post('/checkout', [CheckoutApiController::class, 'store']);
});
