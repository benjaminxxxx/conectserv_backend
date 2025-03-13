<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Profesional extends Authenticatable
{
    use HasFactory;

    protected $table = 'profesionals'; // Nombre de la tabla en la BD

    protected $fillable = [
        'servicio_id',
        'ubicacion_texto',
        'latitud',
        'longitud',
        'nombre',
        'apellido',
        'edad',
        'email',
        'whatsapp',
        'clave',
        'google_id',
        'facebook_id',
    ];

    protected $hidden = ['clave']; // Oculta la clave en respuestas JSON

    public function servicio()
    {
        return $this->belongsTo(Service::class, 'servicio_id');
    }
}
