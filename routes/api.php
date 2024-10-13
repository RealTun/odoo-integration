<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route for initiating authentication
Route::get('/auth', [AuthController::class, 'createAuth']);

Route::get('/auth/callback', [AuthController::class, 'handleCallback']);
// Route::get('/auth/handleToken', [AuthController::class, 'handleToken']);
Route::get('/auth/handleRefreshToken', [AuthController::class, 'handleRefreshToken']);

// use
Route::get('/auth/getAuthorizedShopCipher', [AuthController::class, 'getAuthorizedShopCipher']);

// product
Route::get('/products/all', [ProductController::class, 'getProducts']);
Route::get('/product/{id}', [ProductController::class, 'getProduct']);


// orders
Route::get('/orders/all', [OrderController::class, 'getOrders']);
Route::get('/orders/{id}', [OrderController::class, 'getOrder']);


// webhook
Route::get('/webhook', [WebhookController::class, 'connection']);