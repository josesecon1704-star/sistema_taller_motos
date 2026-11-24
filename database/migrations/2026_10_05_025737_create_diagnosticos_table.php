<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnosticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motocicleta_id')->constrained('motocicletas')->onDelete('cascade');
            $table->unsignedBigInteger('cita_id')->nullable(); // Opcional si viene de una cita
            $table->unsignedBigInteger('empleado_id')->nullable(); // Mecánico asignado
            
            $table->integer('kilometraje_ingreso')->default(0);
            
            // Sistema Motor
            $table->string('estado_motor')->default('BUENO');
            $table->text('notas_motor')->nullable();

            // Sistema Frenos
            $table->string('estado_frenos')->default('BUENO');
            $table->text('notas_frenos')->nullable();

            // Sistema Eléctrico
            $table->string('estado_electrico')->default('BUENO');
            $table->text('notas_electrico')->nullable();

            // Conclusión y detalles
            $table->text('diagnostico_general')->nullable();
            $table->text('descripcion_falla')->nullable();
            $table->date('fecha_diagnostico')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosticos');
    }
};
