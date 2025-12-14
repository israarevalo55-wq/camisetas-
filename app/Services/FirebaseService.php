<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FirebaseService
{
    protected $bucket;
    protected $projectId;

    public function __construct()
    {
        $this->bucket = env('FIREBASE_BUCKET'); // ej: mi-proyecto.appspot.com
        $this->projectId = env('FIREBASE_PROJECT_ID'); // ej: mi-proyecto
    }

    /**
     * Subir imagen a Firebase Storage usando REST API
     */
    public function uploadImage(UploadedFile $file, $folder = 'camisetas'): ?string
    {
        try {
            $filename = $this->generateFilename($file, $folder);
            
            \Log::info('Intentando subir imagen a Firebase', [
                'bucket' => $this->bucket,
                'filename' => $filename,
                'file_size' => $file->getSize()
            ]);

            // Contenido del archivo
            $fileContent = file_get_contents($file->getRealPath());
            $mimeType = $file->getMimeType();

            // URL de Firebase Storage REST API
            $uploadUrl = "https://firebasestorage.googleapis.com/v0/b/{$this->bucket}/o?uploadType=media&name={$filename}";

            // Usar cURL para subir
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uploadUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: {$mimeType}"
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            \Log::info('Respuesta de Firebase', [
                'http_code' => $httpCode,
                'response' => substr($response, 0, 500)
            ]);

            if ($httpCode !== 200) {
                \Log::error('Error al subir a Firebase', [
                    'http_code' => $httpCode,
                    'response' => $response,
                    'curl_error' => $error
                ]);
                return null;
            }

            // Parsear respuesta
            $responseData = json_decode($response, true);
            if (!isset($responseData['name'])) {
                \Log::error('Respuesta inválida de Firebase', ['response' => $response]);
                return null;
            }

            // Generar URL pública
            $url = $this->getPublicUrl($filename);
            
            \Log::info('Imagen subida exitosamente a Firebase', [
                'filename' => $filename,
                'url' => $url
            ]);
            
            return $url;
        } catch (\Exception $e) {
            \Log::error('Error subiendo imagen a Firebase', [
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
        // URL pública de Firebase Storage (sin token de descarga)
        return "https://firebasestorage.googleapis.com/v0/b/{$this->bucket}/o/{$filename}?alt=media";
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
     * Eliminar imagen de Firebase Storage
     */
    public function deleteImage($imageUrl): bool
    {
        try {
            // Extraer el nombre del archivo de la URL
            $filename = $this->extractFilenameFromUrl($imageUrl);
            
            if (!$filename) {
                return false;
            }

            // URL para borrar en Firebase
            $deleteUrl = "https://firebasestorage.googleapis.com/v0/b/{$this->bucket}/o/{$filename}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $deleteUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $httpCode === 204; // 204 No Content = success
        } catch (\Exception $e) {
            \Log::error('Error eliminando imagen de Firebase: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Extraer nombre del archivo de la URL
     */
    protected function extractFilenameFromUrl($imageUrl): ?string
    {
        // URL formato: https://firebasestorage.googleapis.com/v0/b/bucket/o/filename?alt=media
        if (preg_match('/\/o\/(.+?)\?/', $imageUrl, $matches)) {
            return urldecode($matches[1]);
        }
        return null;
    }
}
