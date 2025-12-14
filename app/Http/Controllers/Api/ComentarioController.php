<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    /**
     * Get product comments
     */
    public function index($camisetaId)
    {
        $comentarios = Comentario::where('camiseta_id', $camisetaId)
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $comentarios
        ], 200);
    }

    /**
     * Create comment
     */
    public function store(Request $request)
    {
        $request->validate([
            'camiseta_id' => 'required|exists:camisetas,id',
            'contenido' => 'required|string|max:200'
        ]);

        $user = $request->user();

        $comentario = Comentario::create([
            'user_id' => $user->id,
            'camiseta_id' => $request->camiseta_id,
            'contenido' => $request->contenido
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comentario creado exitosamente',
            'data' => $comentario->load('user:id,name,email')
        ], 201);
    }
}
