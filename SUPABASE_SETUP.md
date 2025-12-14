# Configuración de Supabase en tu Proyecto Laravel

## ✅ Lo que ya está configurado:

1. **SDK de Supabase instalado**: `saeedvir/supabase`
2. **Variables de entorno configuradas** en `.env`:
   ```
   SUPABASE_URL=https://jvcfrpwswxsnvytrbmer.supabase.co
   SUPABASE_KEY=sb_publishable_uiCC3ZYfZKeiQyRVNrQZqg_G-qvTfhp
   SUPABASE_BUCKET=Camisetas
   ```

3. **Archivos creados**:
   - `config/supabase.php` - Configuración centralizada
   - `app/Services/SupabaseService.php` - Servicio para manejar imágenes
   - `app/Http/Controllers/Api/CamisetaController.php` - Actualizado con Supabase

4. **Rutas API funcionales**:
   - `GET /api/test-supabase` - Prueba de conexión
   - `GET /api/camisetas` - Listar camisetas
   - `POST /api/camisetas` - Crear camiseta con imagen

## 🚀 Cómo usar:

### 1. Subir una imagen con una nueva camiseta

```bash
curl -X POST http://localhost:8000/api/camisetas \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: multipart/form-data" \
  -F "nombre=Mi Camiseta" \
  -F "descripcion=Descripción" \
  -F "precio_normal=25" \
  -F "precio_oferta=20" \
  -F "disponible=1" \
  -F "stock=10" \
  -F "plataforma_id=1" \
  -F "imagen=@path/to/image.jpg" \
  -F "genero_id[]=1" \
  -F "genero_id[]=2"
```

### 2. Ver las camisetas con imágenes

```bash
curl -X GET http://localhost:8000/api/camisetas
```

Respuesta esperada:
```json
{
  "success": true,
  "cantidad": 3,
  "data": [
    {
      "id": 1,
      "nombre": "Oversize wash",
      "descripcion": "Calidad algodón premium",
      "precio_normal": "20.00",
      "precio_oferta": null,
      "imagen_url": "https://jvcfrpwswxsnvytrbmer.supabase.co/storage/v1/object/public/Camisetas/camisetas/1734270000_abc123.jpg",
      "disponible": 1,
      "stock": 10,
      "plataforma_id": 1,
      "plataforma": {
        "id": 1,
        "nombre": "Normal"
      },
      "generos": [
        {
          "id": 1,
          "nombre": "Poliester"
        }
      ]
    }
  ]
}
```

## 📁 Estructura de carpetas en Supabase

Las imágenes se guardan en:
- `/Camisetas/camisetas/` - Para camisetas
- El nombre es: `{timestamp}_{random}.{extension}`

Ejemplo: `1734270000_abc123d.jpg`

## ⚙️ SupabaseService - Métodos disponibles:

1. **uploadImage(UploadedFile, folder)**: Sube imagen y retorna URL pública
2. **deleteImage(imageUrl)**: Elimina imagen de Supabase
3. **getPublicUrl(filename)**: Obtiene URL pública de un archivo

## 🔒 Notas de seguridad:

- El token `SUPABASE_KEY` debe estar en `.env` (nunca en Git)
- Las rutas POST/PUT/DELETE están protegidas con middleware `auth:sanctum` y `is_admin`
- Solo administradores pueden crear/editar/eliminar camisetas

## ✨ Próximos pasos:

1. Configurar CORS en Supabase para permitir tu frontend
2. Agregar validación de imágenes más estricta si es necesario
3. Implementar caché de URLs si hay muchas camisetas
