<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;

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


Route::post('/user/delete/{id}', [UserController::class, 'destroy']);
// Protected routes of product and logout
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [UserController::class, 'me']);
    Route::get('/user', [UserController::class, 'index']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::get('/user/search', [UserController::class, 'search']);
    Route::post('/user/register', [LoginRegisterController::class, 'register']);
    Route::get('/user/edit/{id}', [UserController::class, 'show']);
    Route::post('/user/edit/{id}', [UserController::class, 'update']);
    Route::post('/logout', [LoginRegisterController::class, 'logout']);

    Route::controller(ProductController::class)->group(function () {
        Route::get('/getWisata', 'getWisata');
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
