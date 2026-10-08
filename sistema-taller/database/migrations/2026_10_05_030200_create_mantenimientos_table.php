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
            $table->unsignedSmallInteger('intervalo_km')->nullable();
            $table->unsignedTinyInteger('intervalo_mes')->nullable();
            $table->unsignedMediumInteger('ultimo_km')->nullable();
            $table->unsignedMediumInteger('proximo_km')->nullable();
            $table->date('ultima_fecha')->nullable();
            $table->date('proxima_fecha')->nullable();
            $table->string('estado', 20)->default('activo');
            
            $table->foreignId('motocicleta_id')->constrained('motocicletas')->restrictOnDelete();
            $table->foreignId('servicio_id')->constrained('servicios')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['motocicleta_id', 'servicio_id']);
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
