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
        Schema::create('cotizacions', function (Blueprint $table) {
            $table->id();
    
            $table->string('nro_cotizacion', 20)->unique();
            $table->unsignedTinyInteger('version')->default(1);
            $table->string('tipo', 20)->default('inicial');
            $table->dateTime('fecha_emision');
            $table->dateTime('fecha_respuesta')->nullable();
            $table->string('estado', 20)->default('pendiente');
            $table->decimal('total_mano_obra', 10, 2)->default(0);
            $table->decimal('total_repuestos', 10, 2)->default(0);
            $table->decimal('monto_total', 10, 2)->default(0);
            $table->text('observaciones')->nullable();

            $table->foreignId('diagnostico_id')->constrained('diagnosticos')->restrictOnDelete();
            $table->foreignId('orden_trabajo_id')->nullable()->constrained('orden_trabajos')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['diagnostico_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacions');
    }
};
