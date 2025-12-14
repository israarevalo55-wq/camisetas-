<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plataforma;
use Illuminate\Http\Request;

class PlataformaController extends Controller
{
    public function index()
    {
        $plataformas = Plataforma::all();
        return response()->json($plataformas, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'slug' => 'required|string|unique:plataformas,slug'
        ]);

        $plataforma = Plataforma::create($request->all());
        return response()->json(['success' => true, 'data' => $plataforma], 201);
    }

    public function show($id)
    {
        $plataforma = Plataforma::find($id);
        if (!$plataforma) {
            return response()->json(['success' => false, 'message' => 'Plataforma no encontrada'], 404);
        }
        return response()->json(['success' => true, 'data' => $plataforma], 200);
    }

    public function update(Request $request, $id)
    {
        $plataforma = Plataforma::find($id);
        if (!$plataforma) {
            return response()->json(['success' => false, 'message' => 'Plataforma no encontrada'], 404);
        }

        $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'slug' => 'sometimes|string|unique:plataformas,slug,' . $id
        ]);

        $plataforma->update($request->all());
        return response()->json(['success' => true, 'data' => $plataforma], 200);
    }

    public function destroy($id)
    {
        $plataforma = Plataforma::find($id);
        if (!$plataforma) {
            return response()->json(['success' => false, 'message' => 'Plataforma no encontrada'], 404);
        }

        $plataforma->delete();
        return response()->json(['success' => true, 'message' => 'Plataforma eliminada'], 200);
    }
}