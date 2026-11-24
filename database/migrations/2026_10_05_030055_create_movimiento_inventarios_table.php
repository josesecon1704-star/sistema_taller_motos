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
        Schema::create('movimiento_inventarios', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 20);
            $table->string('motivo', 20);
            $table->unsignedInteger('cantidad');
            $table->decimal('costo_unitario', 10, 2)->nullable();
            $table->dateTime('fecha_movimiento');
            $table->text('observaciones')->nullable();
            
            $table->foreignId('repuesto_id')->constrained('repuestos')->restrictOnDelete();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedors')->restrictOnDelete();
            $table->foreignId('uso_repuesto_id')->nullable()->unique()->constrained('uso_repuestos')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_inventarios');
    }
};
