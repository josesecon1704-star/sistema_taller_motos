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
        Schema::create('tarea_servicios', function (Blueprint $table) {
            $table->id();
            
            $table->decimal('precio_mano_obra', 10, 2);
            $table->string('estado', 20)->default('pendiente');
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();
            $table->text('observaciones')->nullable();

            $table->foreignId('orden_trabajo_id')->constrained('orden_trabajos')->restrictOnDelete();
            $table->foreignId('servicio_id')->constrained('servicios')->restrictOnDelete();
            $table->foreignId('empleado_id')->nullable()->constrained('empleados')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarea_servicios');
    }
};
