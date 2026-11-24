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
        Schema::create('detalle_cotizacion_servicios', function (Blueprint $table) {
            $table->id();
            $table->decimal('precio_mano_obra', 10, 2);
            $table->text('observaciones')->nullable();

            $table->foreignId('cotizacion_id')->constrained('cotizacions')->cascadeOnDelete();
            $table->foreignId('servicio_id')->constrained('servicios')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_cotizacion_servicios');
    }
};
