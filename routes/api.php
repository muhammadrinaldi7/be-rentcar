<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\UploadImageController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:api'])->group(function () {
  Route::apiResource('/users', UserController::class);
    Route::apiResource('/cars', CarController::class);  
    Route::apiResource('/bookings', BookingController::class);
  });
  Route::post('/register', RegisterController::class)->name('register');
  Route::post('/login', LoginController::class)->name('login');
  Route::post('/upload-image', [UploadImageController::class, 'upload']);
  Route::get('/messages', [ChatController::class, 'index']);
  Route::post('/messages', [ChatController::class, 'store']);