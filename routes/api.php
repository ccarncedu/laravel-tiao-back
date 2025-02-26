<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LinkController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->get('/check-token', [AuthController::class, 'checkToken']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/links', [LinkController::class, 'index']);
    Route::post('/links', [LinkController::class, 'store']);
    Route::put('/links/{id}', [LinkController::class, 'update']);
    Route::delete('/links/{id}', [LinkController::class, 'destroy']);
    Route::put('/links/{id}/approve', [LinkController::class, 'approve']);
});
