<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::middleware('role:etudiant')->group(function () {
        Route::post('/projets', [ProjetController::class, 'store']);
        Route::post('/projets/{projet}/nouvelle-version', [ProjetController::class, 'deposerVersion']);
    });

    Route::middleware('role:encadreur')->group(function () {
        Route::post('/projets/{projet}/valider', [ProjetController::class, 'valider']);
    });

    Route::get('/projets', [ProjetController::class, 'index']);
    Route::get('/projets/{projet}', [ProjetController::class, 'show']);
});