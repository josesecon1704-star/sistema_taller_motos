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
use App\Http\Controllers\CitaController;               // CU15
use App\Http\Controllers\MantenimientoController;      // CU16
use App\Http\Controllers\DiagnosticoController;        // CU17
use App\Http\Controllers\CotizacionController;         // CU18
use App\Http\Controllers\OrdenTrabajoController;       // CU19, CU20
use App\Http\Controllers\TareaServicioController;      // CU19
use App\Http\Controllers\PagoController;               // CU22
use App\Http\Controllers\GarantiaController;           // CU23
use App\Http\Controllers\ReclamoGarantiaController;   // CU24
use App\Http\Controllers\NotificacionController;       // CU07
use App\Http\Controllers\ReporteController;            // CU25

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

    // CU15 - Gestionar cita (las citas no se eliminan: se cancelan)
    Route::resource('citas', CitaController::class)
        ->except(['destroy'])
        ->parameters(['citas' => 'cita']);
    Route::patch('citas/{cita}/estado', [CitaController::class, 'cambiarEstado'])->name('citas.estado');
    Route::patch('citas/{cita}/cancelar', [CitaController::class, 'cancelar'])->name('citas.cancelar');

    // CU16 - Programar mantenimiento
    Route::resource('mantenimientos', MantenimientoController::class)
        ->only(['index', 'create', 'store', 'update'])
        ->parameters(['mantenimientos' => 'mantenimiento']);

    // CU17 - Registrar diagnóstico
    Route::resource('diagnosticos', DiagnosticoController::class)
        ->only(['index', 'create', 'store'])
        ->parameters(['diagnosticos' => 'diagnostico']);

    // CU18 - Gestionar cotización
    Route::resource('cotizaciones', CotizacionController::class)
        ->only(['index', 'create', 'store']);
    Route::post('/cotizaciones/{id}/estado', [CotizacionController::class, 'cambiarEstado'])->name('cotizaciones.estado');

    // CU19 - Gestionar orden de trabajo
    Route::resource('ordenes-trabajo', OrdenTrabajoController::class)
        ->only(['index', 'store'])
        ->parameters(['ordenes-trabajo' => 'ordenTrabajo']);
    Route::get('/ordenes-trabajo/{ordenTrabajo}/comprobante', [OrdenTrabajoController::class, 'comprobante'])->name('ordenes-trabajo.comprobante');
    Route::patch('/ordenes-trabajo/{ordenTrabajo}/estado', [OrdenTrabajoController::class, 'updateEstado'])->name('ordenes-trabajo.updateEstado');
    Route::post('/tareas-servicios', [TareaServicioController::class, 'store'])->name('tareas-servicios.store');
    Route::patch('/tareas-servicios/{tareaServicio}/estado', [TareaServicioController::class, 'cambiarEstado'])->name('tareas-servicios.estado');
    Route::delete('/tareas-servicios/{tareaServicio}', [TareaServicioController::class, 'destroy'])->name('tareas-servicios.destroy');

    // CU20 - Registrar repuestos utilizados
    Route::post('/ordenes-trabajo/{ordenTrabajo}/repuestos', [OrdenTrabajoController::class, 'storeRepuesto'])->name('ordenes-trabajo.repuestos.store');

    // CU22 - Registrar pago
    Route::post('/ordenes-trabajo/{ordenTrabajo}/pagos', [PagoController::class, 'store'])->name('ordenes-trabajo.pagos.store');

    // CU23 - Registrar garantía / CU24 - Registrar reclamo de garantía
    Route::get('/garantias', [GarantiaController::class, 'index'])->name('garantias.index');
    Route::patch('/garantias/{garantia}', [GarantiaController::class, 'update'])->name('garantias.update');
    Route::post('/garantias/{garantia}/reclamos', [ReclamoGarantiaController::class, 'store'])->name('reclamos-garantias.store');
    Route::patch('/reclamos/{reclamo}/estado', [ReclamoGarantiaController::class, 'cambiarEstado'])->name('reclamos-garantias.estado');

    // CU07 - Visualizar notificaciones
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notifications.index');
    Route::patch('/notificaciones/leer-todas', [NotificacionController::class, 'leerTodas'])->name('notifications.leer-todas');

    // CU25 - Consultar reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');

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
