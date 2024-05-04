<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteUri;
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
Route::get('/media', [MediaController::class, 'index']);
Route::post('/auth/register', [RegisterController::class, 'register']);
Route::post('/auth/login', [LoginController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum', 'throttle:500|2200,1']], function () {
    Route::delete('/media/{media}', [MediaController::class, 'destroy']);
    Route::post('/media', [MediaController::class, 'store']);
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::put('/users/{user}', [UserController::class, 'update']);

    Route::get('/users', [UserController::class, 'getUsers']);
    Route::get('/users/{user}', [UserController::class, 'getUserById']);
});
