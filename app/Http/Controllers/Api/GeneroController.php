<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genero;
use Illuminate\Http\Request;

class GeneroController extends Controller
{
    public function index()
    {
        $generos = Genero::all();
        return response()->json($generos, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'slug' => 'required|string|unique:generos,slug'
        ]);

        $genero = Genero::create($request->all());
        return response()->json(['success' => true, 'data' => $genero], 201);
    }

    public function show($id)
    {
        $genero = Genero::find($id);
        if (!$genero) {
            return response()->json(['success' => false, 'message' => 'Género no encontrado'], 404);
        }
        return response()->json(['success' => true, 'data' => $genero], 200);
    }

    public function update(Request $request, $id)
    {
        $genero = Genero::find($id);
        if (!$genero) {
            return response()->json(['success' => false, 'message' => 'Género no encontrado'], 404);
        }

        $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'slug' => 'sometimes|string|unique:generos,slug,' . $id
        ]);

        $genero->update($request->all());
        return response()->json(['success' => true, 'data' => $genero], 200);
    }

    public function destroy($id)
    {
        $genero = Genero::find($id);
        if (!$genero) {
            return response()->json(['success' => false, 'message' => 'Género no encontrado'], 404);
        }

        $genero->delete();
        return response()->json(['success' => true, 'message' => 'Género eliminado'], 200);
    }
}
 
