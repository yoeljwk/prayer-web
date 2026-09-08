<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PrayerRequestController;
use App\Http\Controllers\Api\PrayerSupportController;
use App\Http\Controllers\Api\PrayerCommentController;
use App\Http\Controllers\Api\PrayerGroupController;

// Public Auth & Prayer Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'TOT API connected successfully',
    ]);
});

Route::get('/prayers', [PrayerRequestController::class, 'index']);
Route::get('/prayers/{prayer}', [PrayerRequestController::class, 'show']);
Route::get('/prayers/{prayer}/comments', [PrayerCommentController::class, 'index']);

Route::get('/groups', [PrayerGroupController::class, 'index']);
Route::get('/groups/{group}', [PrayerGroupController::class, 'show']);

// Protected Routes (Requires Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user/prayers', [PrayerRequestController::class, 'myPrayers']);
    Route::get('/user/supports', [PrayerSupportController::class, 'mySupports']);

    Route::post('/prayers', [PrayerRequestController::class, 'store']);
    Route::put('/prayers/{prayer}', [PrayerRequestController::class, 'update']);
    Route::delete('/prayers/{prayer}', [PrayerRequestController::class, 'destroy']);

    Route::post('/prayers/{prayer}/support', [PrayerSupportController::class, 'store']);
    Route::delete('/prayers/{prayer}/support', [PrayerSupportController::class, 'destroy']);

    Route::post('/prayers/{prayer}/comments', [PrayerCommentController::class, 'store']);

    Route::post('/groups', [PrayerGroupController::class, 'store']);
    Route::put('/groups/{group}', [PrayerGroupController::class, 'update']);
    Route::delete('/groups/{group}', [PrayerGroupController::class, 'destroy']);
    Route::post('/groups/join-by-code', [PrayerGroupController::class, 'joinByCode']);
    Route::post('/groups/{group}/join', [PrayerGroupController::class, 'join']);
    Route::post('/groups/{group}/leave', [PrayerGroupController::class, 'leave']);
    Route::delete('/groups/{group}/members/{userId}', [PrayerGroupController::class, 'removeMember']);
    Route::put('/groups/{group}/members/{userId}/role', [PrayerGroupController::class, 'updateMemberRole']);
});



