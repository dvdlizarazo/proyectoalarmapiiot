<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    use HasFactory;

    protected $table = 'ubicaciones';

    protected $fillable = [
        'nombre_ubicacion',
    ];

    public function entradasSalidas()
    {
        return $this->hasMany(EntradaSalida::class, 'id_ubicacion');
    }
}
