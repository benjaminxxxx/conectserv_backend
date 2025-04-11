<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_id')->constrained('services')->onDelete('cascade');
            $table->string('ubicacion_texto'); // Dirección en texto
            $table->decimal('latitud', 10, 7); // Coordenada de latitud
            $table->decimal('longitud', 10, 7); 
            $table->text('descripcion');
            $table->string('whatsapp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
