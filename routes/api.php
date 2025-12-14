<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GeneroController;
use App\Http\Controllers\Api\PlataformaController;
use App\Http\Controllers\Api\CamisetaController;

use App\Http\Controllers\Api\AuthController;

//Rutas Api 
// 1. Rutas Públicas (Cualquiera entra)
Route::post('/login', [AuthController::class, 'login']);
 
// Catálogo visible para todos (Solo Index y Show)
Route::get('/camisetas', [CamisetaController::class, 'index']);
Route::get('/camisetas/{id}', [CamisetaController::class, 'show']);
Route::get('/plataformas', [PlataformaController::class, 'index']);
 
// 2. Rutas Protegidas (Requieren Token Y ser Admin)
Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
// Logout
    Route::post('/logout', [AuthController::class, 'logout']);
 
    // Gestión completa de Juegos (excepto index/show que ya definimos arriba)
    Route::post('/camisetas', [CamisetaController::class, 'store']);
    Route::put('/camisetas/{id}', [CamisetaController::class, 'update']);
    Route::delete('/camisetas/{id}', [CaisetaController::class, 'destroy']);
 
    // Gestión de Plataformas y Géneros (Todo el CRUD)
    Route::apiResource('plataformas', PlataformaController::class)->except(['index']); // Index es público
    Route::apiResource('generos', GeneroController::class);
});
