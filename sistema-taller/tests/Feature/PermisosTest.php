<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    $this->seed();
});

/** Inicia sesión con una cuenta creada por el seeder. */
function cuenta(string $email): User
{
    return User::where('email', $email)->firstOrFail();
}

it('niega al cliente los módulos del personal', function (string $url) {
    $this->actingAs(cuenta('cliente@taller.com'))->get($url)->assertForbidden();
})->with(['/empleados', '/clientes', '/usuarios', '/servicios/create']);

it('niega al mecánico reportes, garantías y crear citas', function (string $url) {
    $this->actingAs(cuenta('luis@taller.com'))->get($url)->assertForbidden();
})->with(['/reportes', '/garantias', '/citas/create']);

it('niega al jefe de taller la administración del sistema', function (string $url) {
    $this->actingAs(cuenta('jefe@taller.com'))->get($url)->assertForbidden();
})->with(['/usuarios', '/roles', '/bitacora', '/servicios/create']);

it('permite al administrador abrir todos los módulos del menú', function (string $ruta) {
    $this->actingAs(cuenta('admin@taller.com'))->get(route($ruta))->assertOk();
})->with([
    'dashboard',
    'notifications.index',
    'citas.index',
    'motocicletas.index',
    'diagnosticos.index',
    'cotizaciones.index',
    'ordenes-trabajo.index',
    'garantias.index',
    'mantenimientos.index',
    'clientes.index',
    'servicios.index',
    'repuestos.index',
    'proveedores.index',
    'reportes.index',
    'empleados.index',
    'usuarios.index',
    'roles.index',
    'bitacora.index',
]);
