<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitudes extends Model
{
    use HasFactory;

    protected $table = 'solicitudes'; // Especificamos el nombre de la tabla (opcional si sigue la convención)

    protected $fillable = [
        'servicio_id',
        'ubicacion_texto',
        'latitud',
        'longitud',
        'descripcion',
        'whatsapp',
    ];

    public function servicio()
    {
        return $this->belongsTo(Service::class, 'servicio_id');
    }
}
