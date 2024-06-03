<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\TransactionController;

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

// Public routes of authtication
Route::controller(LoginRegisterController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});


// Protected routes of product and logout
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [LoginRegisterController::class, 'me']);
    Route::post('/logout', [LoginRegisterController::class, 'logout']);

    Route::controller(ProductController::class)->group(function () {
        Route::get('/wisata', 'index');
        Route::get('/wisata/{id}', 'show');
        Route::post('/wisata', 'store');
        Route::post('/wisata/{id}', 'update');
        Route::delete('/wisata/{id}', 'destroy');
    });

    Route::controller(TransactionController::class)->group(function () {
        Route::get('/transaction', 'index');
        Route::get('/transaction/{id}', 'show');
        Route::post('/transaction', 'store');
        Route::post('/transaction/{id}', 'update');
        Route::delete('/transaction/{id}', 'destroy');
    });
});
