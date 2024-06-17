<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MasterDeliveryDestinationController;
use App\Http\Controllers\API\MasterProductController;
use App\Http\Controllers\API\TransationDeliveryOrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Routes untuk Produk
    Route::get('/products', [MasterProductController::class, 'index']);
    Route::post('/products', [MasterProductController::class, 'store']);
    Route::put('/products/{id}', [MasterProductController::class, 'update']);
    Route::delete('/products/{id}', [MasterProductController::class, 'destroy']);

    // Routes untuk Tujuan Pengiriman
    Route::get('/destinations', [MasterDeliveryDestinationController::class, 'index']);
    Route::post('/destinations', [MasterDeliveryDestinationController::class, 'store']);
    Route::put('/destinations/{id}', [MasterDeliveryDestinationController::class, 'update']);
    Route::delete('/destinations/{id}', [MasterDeliveryDestinationController::class, 'destroy']);

    // Routes untuk Transaksi
    Route::get('/transactions', [TransationDeliveryOrderController::class, 'index']);
    Route::get('/transactions/{id}', [TransationDeliveryOrderController::class, 'show']);
    Route::post('/transactions', [TransationDeliveryOrderController::class, 'store']);
});
