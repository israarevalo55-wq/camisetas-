<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comentario extends Model
{
    use HasFactory;

    protected $table = 'comentarios';

    protected $fillable = [
        'user_id',
        'camiseta_id',
        'contenido',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function camiseta()
    {
        return $this->belongsTo(Camiseta::class);
    }
}
