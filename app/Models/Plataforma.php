<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; //aggregar para usar factories pruebas


class Plataforma extends Model
{
    use HasFactory;

    //1. mapeo de tabla 
    protected $table = 'plataformas';

    //2.asignacion masiva (columnas que se pueden llenar de forma masiva)
    protected $fillable = [
        'nombre',
        'slug',
    ];

    //3. Relaciones 
    //una camiseta tiene diferetnes diseños

    public function camisetas()
    {   
        //modelo,forigen key de la otra tabla
        return $this->hasMany(Camiseta::class, 'plataforma_id');
    }
}
