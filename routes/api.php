<?php

use App\Http\Controllers\erpnext\WebhookController as ErpnextWebhookController;
use App\Http\Controllers\odoo\CustomerController;
use App\Http\Controllers\odoo\OrderController;
use App\Http\Controllers\odoo\ProductController;
use App\Http\Controllers\odoo\WebhookController as OdooWebhookController;
use App\Http\Controllers\tiktok\AuthController;
use App\Http\Controllers\woocommerce\OrderController as OrderWoo;
use App\Http\Controllers\woocommerce\ProductController as ProductWoo;
use App\Http\Controllers\woocommerce\CustomerController as CustomerWoo;
use App\Http\Controllers\tiktok\ProductController as ProductTik;
use App\Http\Controllers\tiktok\OrderController as OrderTik;
use App\Http\Controllers\tiktok\WebhookController;
use App\Http\Controllers\woocommerce\WooController;
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

Route::prefix('tiktok')->group(function () {
    // Authentication routes
    Route::get('/auth', [AuthController::class, 'createAuth']);
    Route::get('/auth/callback', [AuthController::class, 'handleCallback']);
    Route::get('/auth/handleRefreshToken', [AuthController::class, 'handleRefreshToken']);
    Route::get('/auth/getAuthorizedShopCipher', [AuthController::class, 'getAuthorizedShopCipher']);

    // Product routes
    Route::get('/products', [ProductTik::class, 'getProducts']);
    Route::get('/products/{id}', [ProductTik::class, 'getProduct']);

    // Order routes
    Route::get('/orders', [OrderTik::class, 'getOrders']);
    Route::get('/orders/{id}', [OrderTik::class, 'getOrder']);

    // Webhook routes
    Route::post('/webhook', [WebhookController::class, 'connection']);
    Route::get('/webhook/createSignature', [WebhookController::class, 'createSignature']);
});


Route::prefix('/woo')->group(function () {
    // Customer routes
    Route::prefix('/customers')->group(function() {
        Route::get('', [CustomerWoo::class, 'getCustomers']);
        Route::post('', [CustomerWoo::class, 'createCustomer']);
        Route::get('/{id}', [CustomerWoo::class, 'getCustomer']);
        Route::put('/{id}', [CustomerWoo::class, 'updateCustomer']);
        Route::delete('/{id}', [CustomerWoo::class, 'deleteCustomer']);
    });

    // Product routes
    Route::prefix('/products')->group(function() {
        Route::get('', [ProductWoo::class, 'getProducts']);
        Route::post('', [ProductWoo::class, 'createProduct']);
        Route::get('/{id}', [ProductWoo::class, 'getProduct']);
        Route::put('/{id}', [CustomerWoo::class, 'updateProduct']);
        Route::delete('/{id}', [CustomerWoo::class, 'deleteProduct']);
    });

    // Order routes
    Route::prefix('/orders')->group(function() {
        Route::get('', [OrderWoo::class, 'getOrders']);
        Route::post('', [CustomerWoo::class, 'createOrder']);
        Route::get('/{id}', [CustomerWoo::class, 'getOrder']);
        Route::put('/{id}', [CustomerWoo::class, 'updateCustomer']);
        Route::delete('/{id}', [CustomerWoo::class, 'deleteOrder']);
    });

    // webhook routes
    Route::get('webhook/all', [WooController::class, 'getAllWebhook']);
    Route::post('webhook', [WooController::class, 'handleWebhook']);
});


Route::prefix('/erpnext')->group(function(){
    Route::post('webhook', [ErpnextWebhookController::class, 'handleWebhook']);
});

Route::prefix('/odoo')->group(function(){
    Route::post('webhook', [OdooWebhookController::class, 'handleWebhook']);

    Route::prefix('/customers')->group(function() {
        Route::get('', [CustomerController::class, 'getAllCustomer']);
        Route::post('', [CustomerController::class, 'createCustomer']);
        Route::get('/{id}', [CustomerController::class, 'getCustomer']);
        Route::patch('/{id}', [CustomerController::class, 'updateCustomer']);
        Route::delete('/{id}', [CustomerController::class, 'deleteCustomer']);
    });

    Route::prefix('/products')->group(function() {
        Route::get('', [ProductController::class, 'getAllProduct']);
        Route::post('', [ProductController::class, 'createProduct']);
        Route::get('/{id}', [ProductController::class, 'getProduct']);
        Route::patch('/{id}', [ProductController::class, 'updateProduct']);
        Route::delete('/{id}', [ProductController::class, 'deleteProduct']);
    });

    Route::prefix('/orders')->group(function() {
        Route::get('', [OrderController::class, 'getAllOrder']);
        Route::post('', [OrderController::class, 'createOrder']);
        Route::get('/{id}', [OrderController::class, 'getOrder']);
        Route::patch('/{id}', [OrderController::class, 'updateOrder']);
        Route::delete('/{id}', [OrderController::class, 'deleteOrder']);
    });
});