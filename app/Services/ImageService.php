<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    protected $disk = 'public';
    protected $folder = 'camisetas';

    /**
     * Subir imagen localmente
     */
    public function uploadImage(UploadedFile $file): ?string
    {
        try {
            \Log::info('Intentando guardar imagen localmente', [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize()
            ]);

            // Generar nombre único
            $filename = $this->generateFilename($file);

            // Guardar en storage/app/public/camisetas
            $path = Storage::disk($this->disk)->putFileAs(
                $this->folder,
                $file,
                $filename
            );

            // Generar URL pública
            $url = Storage::disk($this->disk)->url($path);

            \Log::info('Imagen guardada exitosamente', [
                'filename' => $filename,
                'path' => $path,
                'url' => $url
            ]);

            return $url;
        } catch (\Exception $e) {
            \Log::error('Error guardando imagen', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return null;
        }
    }

    /**
     * Generar nombre único para el archivo
     */
    protected function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = time();
        $random = uniqid();
        
        return "{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Eliminar imagen
     */
    public function deleteImage($imageUrl): bool
    {
        try {
            // Extraer el path del storage de la URL
            // URL: http://localhost:8000/storage/camisetas/1734274800_abc123.jpg
            if (preg_match('/\/storage\/(.+)$/', $imageUrl, $matches)) {
                $path = $matches[1];
                
                if (Storage::disk($this->disk)->exists($path)) {
                    Storage::disk($this->disk)->delete($path);
                    \Log::info('Imagen eliminada exitosamente', ['path' => $path]);
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            \Log::error('Error eliminando imagen: ' . $e->getMessage());
            return false;
        }
    }
}
