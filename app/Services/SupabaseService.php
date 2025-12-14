<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class SupabaseService
{
    protected $bucket;
    protected $supabaseUrl;
    protected $supabaseKey;

    public function __construct()
    {
        $this->bucket = env('SUPABASE_BUCKET', 'Camisetas');
        $this->supabaseUrl = env('SUPABASE_URL');
        // Usar SECRET key para operaciones de storage
        $this->supabaseKey = env('SUPABASE_SECRET') ?: env('SUPABASE_KEY');
    }

    /**
     * Subir imagen a Supabase usando API REST
     */
    public function uploadImage(UploadedFile $file, $folder = 'camisetas'): ?string
    {
        try {
            $filename = $this->generateFilename($file, $folder);
            
            \Log::info('Intentando subir imagen a Supabase', [
                'bucket' => $this->bucket,
                'filename' => $filename,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ]);

            // Leer contenido del archivo como binario
            $fileContent = file_get_contents($file->getRealPath());
            $mimeType = $file->getMimeType();

            // URL del endpoint de storage de Supabase
            $uploadUrl = "{$this->supabaseUrl}/storage/v1/object/{$this->bucket}/{$filename}";

            \Log::info('Upload URL', ['url' => $uploadUrl]);

            // Hacer request POST al API de Supabase
            // Para buckets públicos, intentamos sin Authorization o con el público key
            $response = Http::withHeaders([
                'Content-Type' => $mimeType,
            ])
            ->withoutVerifying()
            ->withBody($fileContent, $mimeType)
            ->post($uploadUrl);

            \Log::info('Respuesta de Supabase', [
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 500)
            ]);

            if ($response->failed()) {
                \Log::error('Error al subir a Supabase', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $url = $this->getPublicUrl($filename);
            
            \Log::info('Imagen subida exitosamente', [
                'filename' => $filename,
                'url' => $url
            ]);
            
            return $url;
        } catch (\Exception $e) {
            \Log::error('Error subiendo imagen a Supabase', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return null;
        }
    }

    /**
     * Obtener URL pública de la imagen
     */
    public function getPublicUrl($filename): string
    {
        return "{$this->supabaseUrl}/storage/v1/object/public/{$this->bucket}/{$filename}";
    }

    /**
     * Generar nombre único para el archivo
     */
    protected function generateFilename(UploadedFile $file, $folder = ''): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = time();
        $random = uniqid();
        
        if ($folder) {
            return "{$folder}/{$timestamp}_{$random}.{$extension}";
        }
        
        return "{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Eliminar imagen de Supabase
     */
    public function deleteImage($imageUrl): bool
    {
        try {
            // Extraer el nombre del archivo de la URL
            $filename = $this->extractFilenameFromUrl($imageUrl);
            
            if (!$filename) {
                return false;
            }

            $deleteUrl = "{$this->supabaseUrl}/storage/v1/object/{$this->bucket}/{$filename}";

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->supabaseKey}",
            ])->delete($deleteUrl);

            if ($response->failed()) {
                \Log::error('Error al eliminar de Supabase', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Error eliminando imagen de Supabase: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Extraer nombre del archivo de la URL
     */
    protected function extractFilenameFromUrl($imageUrl): ?string
    {
        $parts = explode("/public/{$this->bucket}/", $imageUrl);
        return $parts[1] ?? null;
    }
}

