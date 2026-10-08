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
        Schema::create('motocicletas', function (Blueprint $table) {
            $table->id();
            $table->string('placa', 10)->nullable()->unique();
            $table->string('nro_chasis', 17)->unique();
            $table->string('marca', 100);
            $table->string('modelo', 100);
            $table->year('anio');
            $table->string('color', 30);
            $table->unsignedSmallInteger('cilindrada');
            $table->string('nro_motor', 30);
            $table->unsignedMediumInteger('kilometraje')->default(0);
            $table->string('foto', 255)->nullable();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motocicletas');
    }
};
