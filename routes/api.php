<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GeneroController;
use App\Http\Controllers\Api\PlataformaController;
use App\Http\Controllers\Api\CamisetaController;
use App\Http\Controllers\Api\FavoritoController;
use App\Http\Controllers\Api\ComentarioController;
use App\Http\Controllers\Api\AuthController;

//Rutas Api 
// 1. Rutas Públicas (Cualquiera entra)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
 
// Catálogo visible para todos (Solo Index y Show)
Route::get('/camisetas', [CamisetaController::class, 'index']);
Route::get('/camisetas/{id}', [CamisetaController::class, 'show']);
Route::get('/plataformas', [PlataformaController::class, 'index']);

// Comentarios públicos (lectura)
Route::get('/camisetas/{camisetaId}/comentarios', [ComentarioController::class, 'index']);
 
// 2. Rutas Protegidas (Requieren Token)
Route::middleware(['auth:sanctum'])->group(function () {
    // Favoritos
    Route::get('/favoritos', [FavoritoController::class, 'index']);
    Route::post('/favoritos', [FavoritoController::class, 'store']);
    Route::delete('/favoritos/{camisetaId}', [FavoritoController::class, 'destroy']);
    Route::get('/favoritos/check/{camisetaId}', [FavoritoController::class, 'isFavorite']);

    // Comentarios (crear)
    Route::post('/comentarios', [ComentarioController::class, 'store']);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});

// 3. Rutas Protegidas (Requieren Token Y ser Admin)
Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
    // Gestión completa de Camisetas (excepto index/show que ya definimos arriba)
    Route::post('/camisetas', [CamisetaController::class, 'store']);
    Route::put('/camisetas/{id}', [CamisetaController::class, 'update']);
    Route::delete('/camisetas/{id}', [CamisetaController::class, 'destroy']);
 
    // Gestión de Plataformas y Géneros (Todo el CRUD)
    Route::apiResource('plataformas', PlataformaController::class)->except(['index']); // Index es público
    Route::apiResource('generos', GeneroController::class);
});
