<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PrayerRequestController;
use App\Http\Controllers\Api\PrayerSupportController;

// Public Auth & Prayer Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'TOT API connected successfully',
    ]);
});

Route::get('/prayers', [PrayerRequestController::class, 'index']);
Route::get('/prayers/{prayer}', [PrayerRequestController::class, 'show']);

// Protected Routes (Requires Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/prayers', [PrayerRequestController::class, 'store']);
    Route::put('/prayers/{prayer}', [PrayerRequestController::class, 'update']);
    Route::delete('/prayers/{prayer}', [PrayerRequestController::class, 'destroy']);

    Route::post('/prayers/{prayer}/support', [PrayerSupportController::class, 'store']);
    Route::delete('/prayers/{prayer}/support', [PrayerSupportController::class, 'destroy']);
});

