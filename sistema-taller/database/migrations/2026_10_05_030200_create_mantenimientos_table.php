<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motocicleta_id')->constrained('motocicletas')->cascadeOnDelete();
            $table->foreignId('servicio_id')->nullable()->constrained('servicios')->nullOnDelete();
            $table->string('tipo_mantenimiento'); // Descripción o nombre del mantenimiento preventivo
            
            // Intervalos y control de alertas
            $table->unsignedSmallInteger('intervalo_km')->nullable();
            $table->unsignedMediumInteger('ultimo_km')->nullable();
            $table->unsignedMediumInteger('proximo_km')->nullable();
            $table->date('ultima_fecha')->nullable();
            $table->date('proxima_fecha'); // Fecha límite fundamental para la alerta por tiempo
            
            // Estados adaptados para el CU17 (PENDIENTE, COMPLETADO, VENCIDO)
            $table->string('estado', 20)->default('PENDIENTE');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};