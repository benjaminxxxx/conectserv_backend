<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificacionWhatsapp extends Model
{
    use HasFactory;
    protected $table = 'verificacion_whatsapps';
    protected $fillable = ['numero','codigo','fecha_expira'];
}
