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
        Schema::create('orden_trabajos', function (Blueprint $table) {
            $table->id();
            $table->string('nro_orden', 20)->unique();
            $table->string('tipo', 20)->default('normal');
            $table->dateTime('fecha_ingreso');
            $table->dateTime('fecha_entrega_estimada')->nullable();
            $table->dateTime('fecha_entrega_real')->nullable();
            $table->unsignedMediumInteger('km_ingreso');
            $table->string('estado', 20)->default('en_proceso');
            $table->decimal('total_mano_obra', 10, 2)->default(0);
            $table->decimal('total_repuestos', 10, 2)->default(0);
            $table->decimal('total_final', 10, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_trabajos');
    }
};
