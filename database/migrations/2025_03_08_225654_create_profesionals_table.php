<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('profesionals', function (Blueprint $table) {
            $table->id(); // ID único del profesional
            $table->foreignId('servicio_id')->constrained('services')->onDelete('cascade'); // Relación con tabla de servicios
            $table->string('ubicacion_texto'); // Dirección en texto
            $table->decimal('latitud', 10, 7); // Coordenada de latitud
            $table->decimal('longitud', 10, 7); // Coordenada de longitud
            $table->string('nombre'); // Nombre del profesional
            $table->string('apellido'); // Apellido del profesional
            $table->integer('edad'); // Edad del profesional
            $table->string('whatsapp')->unique(); // Número de WhatsApp (único)
            $table->string('clave'); // Clave encriptada
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('profesionals');
    }
};
