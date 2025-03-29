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
        Schema::create('entrada_salida', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['entrada', 'salida']);
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_ubicacion');
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamps();

            // Foreign key relationships
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_ubicacion')->references('id')->on('ubicaciones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrada_salida');
    }
};
