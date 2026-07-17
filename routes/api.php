<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\JuryController;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\PresentationController;
use App\Http\Controllers\ProjetController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\SpecialiteController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SemoaCallBackController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::post('authentification', [SemoaCallBackController::class, 'authentification']);
Route::any('semoa-callback-url', [SemoaCallBackController::class, 'authentification'])->name('api.semoa.callback');
Route::post('semoa/create-order-test', [SemoaCallBackController::class, 'createOrder']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    //  Projets (UC1 et UC2) 
    Route::get('/projets', [ProjetController::class, 'index']);
    Route::get('/projets/{projet}', [ProjetController::class, 'show']);

    Route::middleware('role:etudiant')->group(function () {
        Route::post('/projets', [ProjetController::class, 'store']);
        Route::post('/projets/{projet}/nouvelle-version', [ProjetController::class, 'deposerVersion']);
    });

    Route::middleware('role:encadreur')->group(function () {
        Route::post('/projets/{projet}/valider', [ProjetController::class, 'valider']);
    });

    // --- Présentations (UC3) ---
    Route::get('/presentations', [PresentationController::class, 'index']);
    Route::get('/presentations/{presentation}', [PresentationController::class, 'show']);

    Route::middleware('role:administrateur,super_administrateur')->group(function () {
        Route::post('/presentations', [PresentationController::class, 'store']);
        Route::get('/roles', [RoleController::class, 'index']);
        Route::put('/projets/{projet}/affecter-encadreur', [ProjetController::class, 'affecterEncadreur']);

    });

Route::post('semoa/initiate', [PaiementController::class, 'initiate'])->middleware('auth:sanctum');
    //  Saisie de la note par le jury 
    Route::post('/jury/{jury}/note', [JuryController::class, 'saisirNote']);


    //  Utilisateurs (UC4) 
    Route::middleware('role:administrateur,super_administrateur')->group(function () {
        Route::get('/utilisateurs', [UtilisateurController::class, 'index']);
        Route::post('/utilisateurs', [UtilisateurController::class, 'store']);
        Route::get('/utilisateurs/{utilisateur}', [UtilisateurController::class, 'show']);
        Route::put('/utilisateurs/{utilisateur}', [UtilisateurController::class, 'update']);
        Route::delete('/utilisateurs/{utilisateur}', [UtilisateurController::class, 'destroy']);

        Route::apiResource('filieres', FiliereController::class)->except('show');
        Route::apiResource('specialites', SpecialiteController::class)->except('show');
        Route::apiResource('niveaux', NiveauController::class)->except('show');
        Route::apiResource('salles', SalleController::class)->except('show');
        Route::apiResource('promotions', PromotionController::class)->except('show');
    });
});