<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camiseta;
use Illuminate\Http\Request;

class CamisetaController extends Controller
{
    public function index(Request $request)
    {
        $query = Camiseta::with(['plataforma', 'generos'])->where('disponible', true);

        if ($request->has('buscar')) {
            $buscar = $request->input('buscar');
            $query->where('nombre', 'like', '%' . $buscar . '%');
        }

        $camisetas = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'cantidad' => $camisetas->count(),
            'data' => $camisetas
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_oferta' => 'required|numeric',
            'precio_normal' => 'required|numeric',
            'disponible' => 'required|boolean',
            'stock' => 'required|integer',
            'imagen_url' => 'nullable|string',
            'genero_id' => 'array|exists:generos,id',
            'plataforma_id' => 'required|exists:plataformas,id',
        ]);

        $camiseta = Camiseta::create($request->all());

        if ($request->has('genero_id')) {
            $camiseta->generos()->sync($request->input('genero_id'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Camiseta creada exitosamente',
            'data' => $camiseta->load(['plataforma', 'generos'])
        ], 201);
    }

    public function show($id)
    {
        $camiseta = Camiseta::with(['plataforma', 'generos'])->where('disponible', true)->find($id);

        if (!$camiseta) {
            return response()->json([
                'success' => false,
                'message' => 'Camiseta no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $camiseta
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $camiseta = Camiseta::find($id);

        if (!$camiseta) {
            return response()->json([
                'success' => false,
                'message' => 'Camiseta no encontrada'
            ], 404);
        }

        $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|nullable|string',
            'precio_oferta' => 'sometimes|numeric',
            'precio_normal' => 'sometimes|numeric',
            'disponible' => 'sometimes|boolean',
            'stock' => 'sometimes|integer',
            'imagen_url' => 'sometimes|nullable|string',
            'genero_id' => 'sometimes|array|exists:generos,id',
            'plataforma_id' => 'sometimes|exists:plataformas,id',
        ]);

        $camiseta->update($request->all());

        if ($request->has('genero_id')) {
            $camiseta->generos()->sync($request->input('genero_id'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Camiseta actualizada exitosamente',
            'data' => $camiseta->load(['plataforma', 'generos'])
        ], 200);
    }

    public function destroy($id)
    {
        $camiseta = Camiseta::find($id);

        if (!$camiseta) {
            return response()->json([
                'success' => false,
                'message' => 'Camiseta no encontrada'
            ], 404);
        }

        $camiseta->delete();

        return response()->json([
            'success' => true,
            'message' => 'Camiseta eliminada exitosamente'
        ], 200);
    }
}