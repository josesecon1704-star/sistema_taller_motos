<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_trabajos', function (Blueprint $table) {
            $table->id();
            $table->string('nro_orden', 20)->unique();
            
            // Relaciones para el CU23
            $table->unsignedBigInteger('cotizacion_id')->nullable();
            $table->unsignedBigInteger('diagnostico_id')->nullable();

            $table->string('tipo', 20)->default('normal');
            $table->dateTime('fecha_ingreso')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->dateTime('fecha_entrega_estimada')->nullable();
            $table->dateTime('fecha_entrega_real')->nullable();
            $table->unsignedMediumInteger('km_ingreso')->nullable();
            $table->string('estado', 20)->default('activa');
            
            // Totales
            $table->decimal('total_mano_obra', 10, 2)->default(0);
            $table->decimal('total_repuestos', 10, 2)->default(0);
            $table->decimal('monto_total', 10, 2)->default(0);
            $table->decimal('total_final', 10, 2)->default(0);
            
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_trabajos');
    }
};
