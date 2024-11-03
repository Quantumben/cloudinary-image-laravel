<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/product', [\App\Http\Controllers\ProductController::class, 'index']);
Route::post('/product', [\App\Http\Controllers\ProductController::class, 'store']);
