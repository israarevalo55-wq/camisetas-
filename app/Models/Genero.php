<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Genero extends Model
{
    use HasFactory;

    //1. mapeo de tabla 
    protected $table = 'generos';

    //2.asignacion masiva (columnas que se pueden llenar de forma masiva)
    protected $fillable = [
        'nombre',
        'slug',
    ];

    //3. Relaciones 
    //una camiseta tiene diferenTes diseños

    public function camisetas()
    {   
        //modelo,forigen key de la otra tabla
        return $this->hasMany(Camiseta::class, 'genero_id');
    }
}
