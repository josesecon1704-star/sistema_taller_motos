<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('estado', 20)->default('activo');
            $table->timestamp('ultimo_acceso')->nullable();
            $table->foreignId('rol_id')->constrained('rols')->restrictOnDelete();
            $table->foreignId('cliente_id')->nullable()->unique()->constrained('clientes')->restrictOnDelete();
            $table->foreignId('empleado_id')->nullable()->unique()->constrained('empleados')->restrictOnDelete();

            $table->rememberToken();
            $table->timestamps();
        });

        // Regla: la cuenta es de un cliente O de un empleado, exactamente uno
        DB::statement('ALTER TABLE users ADD CONSTRAINT users_cliente_o_empleado CHECK (
            (cliente_id IS NOT NULL AND empleado_id IS NULL) OR
            (cliente_id IS NULL AND empleado_id IS NOT NULL)
        )');

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
