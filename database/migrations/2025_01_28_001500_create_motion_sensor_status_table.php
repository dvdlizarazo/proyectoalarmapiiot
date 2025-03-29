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
        Schema::create('motion_sensor_status', function (Blueprint $table) {
            $table->id(); // ID autoincremental único para cada registro
            $table->string('sensor_name'); // Nombre o identificación del sensor
            $table->boolean('status')->default(false); // Estado del sensor (1 para activo, 0 para inactivo)
            $table->timestamp('detected_at')->nullable(); // Fecha y hora en que se detectó el movimiento
            $table->timestamps(); // Campos created_at y updated_at para registrar fechas de creación y actualización
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motion_sensor_status');
    }
};
