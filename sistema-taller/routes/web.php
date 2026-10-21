<?php

use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\MotocicletaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\RepuestoController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;



Route::redirect('/', '/login');


//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//ruta temporal no olvidar
Route::get('/prueba-finanzas', fn() => 'Entraste a finanzas')
    ->middleware(['auth', 'permiso:finanzas,mostrar']);

//Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

//Servicios
Route::middleware('auth')->group(function () {

    //SERVICIOS
    Route::resource('servicios', ServicioController::class)
        ->except(['show', 'destroy'])
        ->parameters(['servicios' => 'servicio']);

    Route::patch('servicios/{servicio}/estado', [ServicioController::class, 'cambiarEstado'])
        ->name('servicios.estado');

    //REPUESTOS
    Route::resource('repuestos', RepuestoController::class)
        ->except(['show', 'destroy'])
        ->parameters(['repuestos' => 'repuesto']);

    Route::patch('repuestos/{repuesto}/estado', [RepuestoController::class, 'cambiarEstado'])
        ->name('repuestos.estado');

    Route::get('repuestos/{repuesto}/movimiento', [RepuestoController::class, 'movimiento'])
        ->name('repuestos.movimiento');

    Route::post('repuestos/{repuesto}/movimiento', [RepuestoController::class, 'registrarMovimiento'])
        ->name('repuestos.movimiento.store');

    //PROVEEDOR
    Route::resource('proveedores', ProveedorController::class)
        ->except(['show', 'destroy'])
        ->parameters(['proveedores' => 'proveedor']);

    Route::patch('proveedores/{proveedor}/estado', [ProveedorController::class, 'cambiarEstado'])
        ->name('proveedores.estado');

    Route::get('proveedores/{proveedor}/repuestos', [ProveedorController::class, 'repuestos'])
        ->name('proveedores.repuestos');

    Route::post('proveedores/{proveedor}/repuestos', [ProveedorController::class, 'vincularRepuesto'])
        ->name('proveedores.repuestos.store');

    Route::put('proveedores/{proveedor}/repuestos/{repuesto}', [ProveedorController::class, 'actualizarVinculo'])
        ->name('proveedores.repuestos.update');

    Route::delete('proveedores/{proveedor}/repuestos/{repuesto}', [ProveedorController::class, 'desvincularRepuesto'])
        ->name('proveedores.repuestos.destroy');

    //CLIENTES
    Route::resource('clientes', ClienteController::class)
        ->except(['destroy'])
        ->parameters(['clientes' => 'cliente']);

    Route::get('clientes/{cliente}/cuenta', [ClienteController::class, 'cuenta'])
        ->name('clientes.cuenta');

    Route::post('clientes/{cliente}/cuenta', [ClienteController::class, 'guardarCuenta'])
        ->name('clientes.cuenta.store');

    //EMPLEADOS
    Route::resource('empleados', EmpleadoController::class)
        ->except(['destroy'])
        ->parameters(['empleados' => 'empleado']);

    Route::patch('empleados/{empleado}/estado', [EmpleadoController::class, 'cambiarEstado'])
        ->name('empleados.estado');

    Route::get('empleados/{empleado}/cuenta', [EmpleadoController::class, 'cuenta'])
        ->name('empleados.cuenta');
    Route::post('empleados/{empleado}/cuenta', [EmpleadoController::class, 'guardarCuenta'])
        ->name('empleados.cuenta.store');

    //BITACORA
    Route::get('bitacora', [BitacoraController::class, 'index'])
        ->middleware('permiso:bitacora,mostrar')
        ->name('bitacora.index');

    //MOTOCICLETA
    Route::resource('motocicletas', MotocicletaController::class)
        ->except(['destroy'])
        ->parameters(['motocicletas' => 'motocicleta']);

    Route::get('roles', [RolController::class, 'index'])->name('roles.index');
    Route::get('roles/{rol}/permisos', [RolController::class, 'permisos'])->name('roles.permisos');
    Route::put('roles/{rol}/permisos', [RolController::class, 'actualizarPermisos'])->name('roles.permisos.update');

    //Usuarios
    Route::resource('usuarios', UsuarioController::class)
        ->only(['index', 'edit', 'update'])
        ->parameters(['usuarios' => 'usuario']);
    Route::patch('usuarios/{usuario}/estado', [UsuarioController::class, 'cambiarEstado'])->name('usuarios.estado');
});


require __DIR__ . '/auth.php';
