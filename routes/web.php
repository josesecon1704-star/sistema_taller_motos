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
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\CotizacionController; // CU19
use App\Http\Controllers\CitaController;
use App\Http\Controllers\MantenimientoController; // CU17
use App\Http\Controllers\DiagnosticoController; // CU18
use App\Http\Controllers\OrdenTrabajoController; // CU23 / CU25 / CU26
use App\Http\Controllers\TareaServicioController; // CU24
use App\Http\Controllers\PagoController; // CU22
use App\Http\Controllers\ReclamoGarantiaController;

Route::redirect('/', '/login');

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

    //CITAS (CU15)
    Route::resource('citas', CitaController::class)
        ->parameters(['citas' => 'cita']);

    Route::patch('citas/{cita}/estado', [CitaController::class, 'cambiarEstado'])
        ->name('citas.estado');

    //MANTENIMIENTOS (CU17)
    Route::resource('mantenimientos', MantenimientoController::class)
        ->parameters(['mantenimientos' => 'mantenimiento']);

    //DIAGNOSTICOS (CU18)
    Route::resource('diagnosticos', DiagnosticoController::class)
        ->parameters(['diagnosticos' => 'diagnostico']);

    //COTIZACIONES (CU19)
    Route::resource('cotizaciones', CotizacionController::class)
        ->parameters(['cotizacions' => 'cotizacion']);

    Route::post('/cotizaciones/{id}/estado', [CotizacionController::class, 'cambiarEstado'])
        ->name('cotizaciones.estado');

    //ORDENES DE TRABAJO (CU23 / CU25)
    Route::resource('ordenes-trabajo', OrdenTrabajoController::class)
        ->parameters(['ordenes-trabajo' => 'ordenTrabajo']);

    // CU27 - Ruta para ver e imprimir el reporte o comprobante de servicio
    Route::get('/ordenes-trabajo/{ordenTrabajo}/comprobante', [OrdenTrabajoController::class, 'comprobante'])
        ->name('ordenes-trabajo.comprobante');

    // CU25 - Actualizar Estado de Orden de Trabajo
    Route::patch('/ordenes-trabajo/{ordenTrabajo}/estado', [OrdenTrabajoController::class, 'updateEstado'])
        ->name('ordenes-trabajo.updateEstado');

    // CU26 - Registrar Repuestos en la Orden de Trabajo
    Route::post('/ordenes-trabajo/{ordenTrabajo}/repuestos', [OrdenTrabajoController::class, 'storeRepuesto'])
        ->name('ordenes-trabajo.repuestos.store');

    // CU29 - Registrar Pago en la Orden de Trabajo
    Route::post('/ordenes-trabajo/{ordenTrabajo}/pagos', [PagoController::class, 'store'])
        ->name('ordenes-trabajo.pagos.store');

    // CU30 / CU31 - Registrar Reclamo de Garantía
    Route::post('/garantias/{garantia}/reclamos', [ReclamoGarantiaController::class, 'store'])
        ->name('reclamos-garantias.store');

    //TAREAS DE SERVICIO / ASIGNAR A MECÁNICOS (CU24)
    Route::post('/tareas-servicios', [TareaServicioController::class, 'store'])
        ->name('tareas-servicios.store');
    Route::patch('/tareas-servicios/{tareaServicio}/estado', [TareaServicioController::class, 'cambiarEstado'])
        ->name('tareas-servicios.estado');
    Route::delete('/tareas-servicios/{tareaServicio}', [TareaServicioController::class, 'destroy'])
        ->name('tareas-servicios.destroy');

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

    //notificaciones
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notifications.index');
});

require __DIR__ . '/auth.php';