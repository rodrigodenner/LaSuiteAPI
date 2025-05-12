<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RegimeController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return response()->json([
    'message' => 'BEM VINDO A API La Suite '
  ]);
});

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
    Route::put('/{roomId}/tariffs', [RoomController::class, 'updateTariffs']);
    Route::delete('/{id}', [RoomController::class, 'destroy']);
  });

  Route::prefix('reservations')->group(function () {
    Route::get('/', [ReservationController::class, 'index']);
    Route::post('/', [ReservationController::class, 'store']);
    Route::get('/{reservationId}', [ReservationController::class, 'show']);
    Route::put('/{reservationId}', [ReservationController::class, 'update']);
    Route::delete('/{reservationId}', [ReservationController::class, 'destroy']);

    Route::get('/{reservationId}/payment', [PaymentController::class, 'show']);
    Route::post('/{reservationId}/pay', [PaymentController::class, 'pay']);
  });
});



