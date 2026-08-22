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
use App\Http\Controllers\AdministrateurController;
use App\Http\Controllers\StatsAdminController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\StatsEtudiantController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StatsEncadreurController;
use App\Http\Controllers\StatsJuryController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\HistoriqueController;
use Illuminate\Support\Facades\Route;


Route::post('/mot-de-passe-oublie', [AuthController::class, 'motDePasseOublie']);
Route::post('/reinitialiser-mot-de-passe', [AuthController::class, 'reinitialiserMotDePasse']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('authentification', [SemoaCallBackController::class, 'authentification']);
Route::any('semoa-callback-url', [SemoaCallBackController::class, 'authentification'])->name('api.semoa.callback');
Route::post('semoa/create-order-test', [SemoaCallBackController::class, 'createOrder']);

Route::post('/system/trigger-backup', [SystemController::class, 'declencherSauvegarde']);
Route::get('/system/migrate-status', [SystemController::class, 'statutMigrations']);
Route::post('/system/migrate', [SystemController::class, 'lancerMigrations']);
Route::get('/system/diagnostic-cors', [SystemController::class, 'diagnosticCors']);



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'modifierProfil']);
    Route::put('/me/mot-de-passe', [AuthController::class, 'changerMotDePasse']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/marquer-lu', [NotificationController::class, 'marquerLu']);
    Route::post('/notifications/marquer-tout-lu', [NotificationController::class, 'marquerToutLu']);

    //  Projets (UC1 et UC2) 
    Route::get('/projets', [ProjetController::class, 'index']);
    Route::get('/projets/{projet}', [ProjetController::class, 'show']);

    Route::middleware('role:etudiant')->group(function () {
        Route::get('/stats-etudiant/dashboard', [StatsEtudiantController::class,'dashboard']);
        Route::post('/projets', [ProjetController::class, 'store']);
        Route::post('/projets/{projet}/nouvelle-version', [ProjetController::class, 'deposerVersion']);
    });

    Route::middleware('role:encadreur')->group(function () {
        Route::get('/stats-encadreur/dashboard', [StatsEncadreurController::class, 'dashboard']);
        Route::post('/projets/{projet}/valider', [ProjetController::class, 'valider']);
    });

    Route::middleware('role:jury_externe,encadreur')->group(function () {
    Route::get('/stats-jury/dashboard', [StatsJuryController::class, 'dashboard']);
    });

    // --- Présentations (UC3) ---
    Route::get('/presentations', [PresentationController::class, 'index']);
    Route::get('/presentations/{presentation}', [PresentationController::class, 'show']);

    Route::middleware('role:administrateur,super_administrateur')->group(function () {
        Route::delete('/presentations/{presentation}', [PresentationController::class, 'annuler']);
        Route::post('/presentations/verifier-disponibilite', [PresentationController::class, 'verifierDisponibilite']);
        Route::post('/presentations', [PresentationController::class, 'store']);
        Route::put('/presentations/{presentation}', [PresentationController::class, 'update']);
        Route::get('/roles', [RoleController::class, 'index']);
        Route::put('/projets/{projet}/affecter-encadreur', [ProjetController::class, 'affecterEncadreur']);
        Route::patch('/projets/{projet}/statut', [ProjetController::class, 'changerStatut']);

    });

    Route::middleware('role:super_administrateur')->group(function () {
        Route::get('/stats-admin/global', [StatsAdminController::class, 'global']);
        Route::get('/roles-permissions', [RolePermissionController::class, 'index']);
        Route::put('/roles/{role}/permissions', [RolePermissionController::class, 'update']);
        Route::get('/administrateurs', [AdministrateurController::class, 'index']);
        Route::post('/administrateurs', [AdministrateurController::class, 'store']);
        Route::put('/administrateurs/{utilisateur}', [AdministrateurController::class, 'update']);
        Route::patch('/administrateurs/{utilisateur}/toggle-actif', [AdministrateurController::class, 'toggleActif']);
        Route::delete('/administrateurs/{utilisateur}', [AdministrateurController::class, 'destroy']);
        Route::post('/administrateurs/{utilisateur}/renvoyer-identifiants', [AdministrateurController::class, 'renvoyerIdentifiants']);
        Route::get('/historique', [HistoriqueController::class, 'index']);
   
});

Route::post('semoa/initiate', [PaiementController::class, 'initiate'])->middleware('auth:sanctum');
    //  Saisie de la note par le jury
        Route::get('/jury/{jury}', [JuryController::class, 'show']);
    Route::post('/jury/{jury}/note', [JuryController::class, 'saisirNote']);


    //  Utilisateurs (UC4) 
    Route::middleware('role:administrateur,super_administrateur')->group(function () {
        Route::delete('/projets/{projet}', [ProjetController::class, 'destroy']);
        Route::get('/stats-admin/dashboard', [StatsAdminController::class, 'dashboard']);
        Route::get('/utilisateurs', [UtilisateurController::class, 'index']);
        Route::post('/utilisateurs', [UtilisateurController::class, 'store']);
        Route::get('/utilisateurs/{utilisateur}', [UtilisateurController::class, 'show']);
        Route::put('/utilisateurs/{utilisateur}', [UtilisateurController::class, 'update']);
        Route::delete('/utilisateurs/{utilisateur}', [UtilisateurController::class, 'destroy']);
        Route::post('/utilisateurs/{utilisateur}/renvoyer-identifiants', [UtilisateurController::class, 'renvoyerIdentifiants']);
        Route::get('/parametres', [ParametreController::class, 'index']);
        Route::put('/parametres', [ParametreController::class, 'update']);

        Route::apiResource('filieres', FiliereController::class)->except('show');
        Route::apiResource('specialites', SpecialiteController::class)->except('show');
        Route::apiResource('niveaux', NiveauController::class)->except('show');
        Route::apiResource('salles', SalleController::class)->except('show');
        Route::apiResource('promotions', PromotionController::class)->except('show');
    });
});