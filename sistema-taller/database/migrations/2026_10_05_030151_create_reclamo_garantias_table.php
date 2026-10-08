<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */

    /** 
     * orden_trabajo_id es la orden de reparación, no la original. 
     * Queda vacía mientras el reclamo está pendiente o se rechaza, y se llena solo si se aprueba.
     */
    public function up(): void
    {
        Schema::create('reclamo_garantias', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->unsignedMediumInteger('km');
            $table->string('descripcion', 255);
            $table->string('estado', 20)->default('pendiente');
            $table->foreignId('garantia_id')->constrained('garantias')->restrictOnDelete();
            $table->foreignId('orden_trabajo_id')->nullable()->unique()->constrained('orden_trabajos')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reclamo_garantias');
    }
};
