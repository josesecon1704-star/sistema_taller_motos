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
        Schema::create('detalle_cotizacion_repuestos', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            
            $table->foreignId('cotizacion_id')->constrained('cotizacions')->cascadeOnDelete();
            $table->foreignId('repuesto_id')->constrained('repuestos')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_cotizacion_repuestos');
    }
};
