<?php

use App\Http\Middleware\JWTMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware([JWTMiddleware::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/{id}', [TaskController::class, 'show']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
});
