<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('shopify')->group(function(){
    Route::get('/products', [ProductController::class, 'index'])->name('shopify-products');
});
