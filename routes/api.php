<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClickController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// User routes
Route::post('/login', [AuthController::class, 'login']);
Route::group(['middleware' => 'auth:api'], function () {
    Route::group(['middleware' => 'admin'], function () {
        Route::get('/user', [UserController::class, 'index']);
        Route::post('/user', [UserController::class, 'create']);
    });
    Route::get('/profile', [AuthController::class, 'show']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Network routes
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/network', [NetworkController::class, 'index']);
    Route::post('/network', [NetworkController::class, 'create'])->middleware('admin');
    Route::put('/network', [NetworkController::class, 'hide'])->middleware('admin');
    Route::delete('/network', [NetworkController::class, 'delete'])->middleware('admin');
    Route::get('/network/pb', [NetworkController::class, 'showNetworkPostbackUrl']);
});

// Offer routes

Route::group(['middleware' => 'auth:api', 'cors'], function () {
    Route::get('/offer', [OfferController::class, 'index']);
    Route::group(['middleware' => 'admin'], function () {
        Route::post('/offer', [OfferController::class, 'create'])->middleware('admin');
        Route::put('/offer', [OfferController::class, 'hide'])->middleware('admin');
        Route::delete('/offer', [OfferController::class, 'delete'])->middleware('admin');
    });
});


// Click routes

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/click', [ClickController::class, 'index']);
});




