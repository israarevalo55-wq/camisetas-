<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorito;
use App\Models\Camiseta;
use Illuminate\Http\Request;

class FavoritoController extends Controller
{
    /**
     * Get user's favorites
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $favoritos = Favorito::where('user_id', $user->id)
            ->with(['camiseta.plataforma', 'camiseta.generos'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $favoritos->pluck('camiseta')
        ], 200);
    }

    /**
     * Add to favorites
     */
    public function store(Request $request)
    {
        $request->validate([
            'camiseta_id' => 'required|exists:camisetas,id'
        ]);

        $user = $request->user();

        // Verificar si ya está en favoritos
        $existe = Favorito::where('user_id', $user->id)
            ->where('camiseta_id', $request->camiseta_id)
            ->first();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'Ya está en favoritos'
            ], 400);
        }

        Favorito::create([
            'user_id' => $user->id,
            'camiseta_id' => $request->camiseta_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Agregado a favoritos'
        ], 201);
    }

    /**
     * Remove from favorites
     */
    public function destroy(Request $request, $camisetaId)
    {
        $user = $request->user();

        $favorito = Favorito::where('user_id', $user->id)
            ->where('camiseta_id', $camisetaId)
            ->first();

        if (!$favorito) {
            return response()->json([
                'success' => false,
                'message' => 'No está en favoritos'
            ], 404);
        }

        $favorito->delete();

        return response()->json([
            'success' => true,
            'message' => 'Removido de favoritos'
        ], 200);
    }

    /**
     * Check if product is favorite
     */
    public function isFavorite(Request $request, $camisetaId)
    {
        $user = $request->user();

        $isFavorite = Favorito::where('user_id', $user->id)
            ->where('camiseta_id', $camisetaId)
            ->exists();

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite
        ], 200);
    }
}
