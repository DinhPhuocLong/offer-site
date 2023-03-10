<?php

use App\Http\Controllers\ClickController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Click routes
Route::get('/click', [ClickController::class, 'create']);


// Link lead routes
Route::get('/click/l', [ClickController::class, 'createLeadClick']);


// Receive Postback routes
Route::get('/pb', [ClickController::class, 'createConversion']);
