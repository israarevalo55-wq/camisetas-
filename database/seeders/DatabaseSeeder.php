<?php
 
namespace Database\Seeders;
 
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
 
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear Plataformas
        $Regular = \App\Models\Plataforma::create(['Nombre' => 'Normal', 'slug' => 'normal']);
        $Oversize = \App\Models\Plataforma::create(['Nombre' => 'Oversize', 'slug' => 'oversize']);
 
        // 2. Crear Géneros
        $Poliester = \App\Models\Genero::create(['nombre' => 'Poliester', 'slug' => 'poliester']);
        $Algodon = \App\Models\Genero::create(['nombre' => 'Algodon', 'slug' => 'algodon']);
 
        // 3. Crear Camiseta
        $Camiseta = \App\Models\Camiseta::create([
            'nombre' => 'Oversize wash',
            'descripcion' => 'Calidad algodón premium',
            'precio_normal' => 20.00,
            'plataforma_id' => $Regular->id,
            'disponible' => true,
            'stock' => 10
        ]);
 
        // 4. Vincular géneros (Tabla Pivote)
        $Camiseta->generos()->attach([$Poliester->id, $Algodon->id]);
        
        // 5. Usuario Admin
        \App\Models\User::create([
            'name' => 'Israel',
            'email' => 'israel@gamezone.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin'
        ]);
    }
}