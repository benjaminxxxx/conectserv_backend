<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class Profesional extends Authenticatable
{
    use HasFactory;

    protected $table = 'profesionales'; // Nombre de la tabla en la BD

    protected $fillable = [
        'user_id',
        'ubicacion_texto',
        'latitud',
        'longitud',
        'whatsapp',
        'verificado',
        'imagen_identidad_frontal',
        'imagen_identidad_dorso',
        'imagen_real'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'profesional_servicio');
    }
    public function getUrlImagenIdentidadFrontalAttribute(){
        return $this->imagen_identidad_frontal ? Storage::disk('public')->url($this->imagen_identidad_frontal) : null;
    }
    public function getUrlImagenIdentidadDorsoAttribute(){
        return $this->imagen_identidad_dorso ? Storage::disk('public')->url($this->imagen_identidad_dorso) : null;
    }
    public function getUrlImagenRealAttribute(){
        return $this->imagen_real ? Storage::disk('public')->url($this->imagen_real) : null;
    }
}
