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
        Schema::create('garantias', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_inicio');
            $table->unsignedSmallInteger('dias');
            $table->unsignedSmallInteger('km');
            $table->string('cobertura', 255)->nullable();
            $table->string('estado', 20)->default('vigente');
            
            $table->foreignId('orden_trabajo_id')->unique()->constrained('orden_trabajos')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garantias');
    }
};
