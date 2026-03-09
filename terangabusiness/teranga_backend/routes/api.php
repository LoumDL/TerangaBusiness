<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CotisationController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\HistoriqueController;
use App\Http\Controllers\Api\PaiementController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Routes publiques
    Route::post('/auth/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1');
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');

    // Webhook PayDunya — public, pas de auth (appelé par PayDunya)
    Route::post('/paiements/webhook', [PaiementController::class, 'webhook']);

    // Mock checkout — développement uniquement
    Route::get('/paiements/mock-checkout/{token}', [PaiementController::class, 'mockCheckout']);

    // Routes protégées
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/cotisations', [CotisationController::class, 'index']);
        Route::get('/historique', [HistoriqueController::class, 'index']);
        Route::delete('/historique/{id}', [HistoriqueController::class, 'destroy']);
        Route::post('/paiements', [PaiementController::class, 'store']);
        Route::post('/paiements/confirm', [PaiementController::class, 'confirm']);
        Route::get('/paiements/{id}', [PaiementController::class, 'show']);
        Route::get('/paiements/{id}/status', [PaiementController::class, 'status']);
    });
});
