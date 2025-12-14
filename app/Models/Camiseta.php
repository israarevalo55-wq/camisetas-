<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Plataforma;
use App\Models\Genero;

class Camiseta extends Model
{
    use HasFactory;

    //1. mapeo de tabla
    protected $table = 'camisetas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_oferta',
        'precio_normal',
        'disponible',
        'stock',
        'imagen_url',
        'plataforma_id', //importante para agregar la fk
    ];

    //3. Relaciones
    //una camiseta pertenece a una plataforma
    public function plataforma()
    {
        return $this->belongsTo(Plataforma::class, 'plataforma_id');
    }
    
    //relacion n a n con generos
    public function generos()
    {
        return $this->belongsToMany(Genero::class, 'camiseta_genero', 'camiseta_id', 'genero_id');
    }

    // Favoritos
    public function favoritos()
    {
        return $this->hasMany(Favorito::class);
    }

    // Comentarios
    public function comentarios()
    {
        return $this->hasMany(Comentario::class);
    }
