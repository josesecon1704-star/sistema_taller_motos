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
        Schema::create('proveedor_repuestos', function (Blueprint $table) {
            $table->id();
            $table->decimal('precio_compra', 10, 2);
            $table->string('codigo_proveedor', 30)->nullable();
            $table->foreignId('proveedor_id')->constrained('proveedors')->cascadeOnDelete();
            $table->foreignId('repuesto_id')->constrained('repuestos')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['proveedor_id', 'repuesto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedor_repuestos');
    }
};
