<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('profesionales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('servicio_id')->nullable()->constrained('services')->onDelete('set null');
            $table->string('ubicacion_texto')->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->string('whatsapp')->nullable();
            $table->boolean('verificado')->default(false);
            $table->text('imagen_identidad_frontal')->nullable();
            $table->text('imagen_identidad_dorso')->nullable();
            $table->text('imagen_real')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('profesionales');
    }
};
