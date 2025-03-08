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
        Schema::create('verificacion_whatsapps', function (Blueprint $table) {
            $table->id();
            $table->string('numero'); // Número al que se envía el código
            $table->string('codigo'); // Código de 6 dígitos
            $table->timestamp('fecha_expira'); // Tiempo de expiración
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verificacion_whatsapps');
    }
};
