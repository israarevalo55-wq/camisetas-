<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camiseta;
use Illuminate\Http\Request;
use Saeedvir\Supabase\Facades\Supabase;

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
            'imagen' => 'nullable|image|max:2048',
            'genero_id' => 'array|exists:generos,id',
            'plataforma_id' => 'required|exists:plataformas,id',
        ]);

        $data = $request->all();

        // Subir imagen a Supabase si existe
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $bucket = env('SUPABASE_BUCKET');

            Supabase::storage()->upload(
                $bucket,
                $filename,
                $file->getPathname()
            );

            // Guardar URL pública en la tabla
            $data['imagen_url'] = env('SUPABASE_URL') . "/storage/v1/object/public/{$bucket}/{$filename}";
        }

        $camiseta = Camiseta::create($data);

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
            'imagen' => 'sometimes|image|max:2048',
            'genero_id' => 'sometimes|array|exists:generos,id',
            'plataforma_id' => 'sometimes|exists:plataformas,id',
        ]);

        $data = $request->all();

        // Subir nueva imagen si se envía
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $bucket = env('SUPABASE_BUCKET');

            Supabase::storage()->upload(
            $bucket,
            $filename,
            file_get_contents($file)
      );
            $data['imagen_url'] = env('SUPABASE_URL') . "/storage/v1/object/public/{$bucket}/{$filename}";
        }

        $camiseta->update($data);

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