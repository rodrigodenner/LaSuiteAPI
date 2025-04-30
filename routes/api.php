<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\RegimeController;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Auth routes
Route::post('/generate-token', [AuthController::class, 'generateToken']);

Route::middleware(['auth:sanctum'])->group(function () {
  Route::prefix('regimes')->group(function () {
    Route::get('/', [RegimeController::class, 'index']);
    Route::get('/{id}', [RegimeController::class, 'show']);
    Route::post('/', [RegimeController::class, 'store']);
    Route::put('/{id}', [RegimeController::class, 'update']);
    Route::delete('/{id}', [RegimeController::class, 'destroy']);
  });

  Route::prefix('rooms')->group(function () {
    Route::get('/', [RoomController::class, 'index']);
    Route::get('/available', [RoomController::class, 'available']);
    Route::get('/{slug}', [RoomController::class, 'show']);
    Route::post('/', [RoomController::class, 'store']);
    Route::put('/{id}', [RoomController::class, 'update']);
    Route::delete('/{id}', [RoomController::class, 'destroy']);
  });

  Route::prefix('guest')->group(function () {
    Route::get('/', [GuestController::class, 'index']);
    Route::post('/', [GuestController::class, 'store']);
    Route::get('/{id}', [GuestController::class, 'show']);
    Route::put('/{id}', [GuestController::class, 'update']);
    Route::delete('/{id}', [GuestController::class, 'destroy']);
  });
});



