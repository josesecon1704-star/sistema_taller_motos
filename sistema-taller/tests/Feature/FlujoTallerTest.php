<?php

use App\Models\{Cita, Cotizacion, Diagnostico, Garantia, MovimientoInventario, Notificacion, OrdenTrabajo, Pago, Repuesto, Servicio, User, Motocicleta, Cliente, ReclamoGarantia};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    $this->seed();
    $this->admin = User::where('email', 'admin@taller.com')->first();
    $this->jefe = User::where('email', 'jefe@taller.com')->first();
    $this->meca = User::where('email', 'luis@taller.com')->first();
    $this->cli = User::where('email', 'cliente@taller.com')->first();
    $otro = Cliente::create(['nombre' => 'Otro', 'tipo_documento' => 'ci', 'numero_documento' => '999', 'telefono' => '7', 'tipo' => 'particular']);
    $mk = fn ($c, $p) => Motocicleta::create(['cliente_id' => $c, 'placa' => $p, 'nro_chasis' => 'CH' . $p, 'marca' => 'Honda', 'modelo' => 'XR', 'anio' => 2020, 'color' => 'rojo', 'cilindrada' => 150, 'nro_motor' => 'M' . $p, 'kilometraje' => 10000]);
    $this->moto = $mk($this->cli->cliente_id, '111AAA');
    $this->motoOtro = $mk($otro->id, '222BBB');
});

function manana() { return now()->addDay()->format('Y-m-d'); }

it('flujo completo de citas a garantia', function () {
    // CU15: cliente agenda con su moto
    $this->actingAs($this->cli)->post('/citas', ['motocicleta_id' => $this->moto->id, 'fecha' => manana(), 'hora' => '10:00', 'motivo' => 'Ruido raro'])->assertRedirect();
    $cita = Cita::first();
    expect($cita->estado)->toBe('programada')->and($cita->cliente_id)->toBe($this->cli->cliente_id);
    // con moto ajena: rechazado
    $this->actingAs($this->cli)->post('/citas', ['motocicleta_id' => $this->motoOtro->id, 'fecha' => manana(), 'hora' => '11:00', 'motivo' => 'x'])->assertSessionHasErrors('motocicleta_id');
    // segunda cita misma moto: rechazado
    $this->actingAs($this->cli)->post('/citas', ['motocicleta_id' => $this->moto->id, 'fecha' => manana(), 'hora' => '12:00', 'motivo' => 'x'])->assertSessionHasErrors('motocicleta_id');
    // fecha pasada
    $this->actingAs($this->jefe)->post('/citas', ['motocicleta_id' => $this->motoOtro->id, 'fecha' => now()->subDay()->format('Y-m-d'), 'hora' => '12:00', 'motivo' => 'x'])->assertSessionHasErrors('fecha');
    // mecanico no confirma
    $this->actingAs($this->meca)->patch("/citas/{$cita->id}/estado", ['estado' => 'confirmada'])->assertForbidden();
    // jefe confirma -> notifica al cliente
    $this->actingAs($this->jefe)->patch("/citas/{$cita->id}/estado", ['estado' => 'confirmada'])->assertRedirect();
    expect($cita->fresh()->estado)->toBe('confirmada');
    expect(Notificacion::where('user_id', $this->cli->id)->count())->toBeGreaterThan(0);
    // transicion invalida
    $this->actingAs($this->jefe)->patch("/citas/{$cita->id}/estado", ['estado' => 'programada'])->assertSessionHas('error');

    // CU17: diagnostico del mecanico completa la cita
    $this->actingAs($this->meca)->post('/diagnosticos', ['motocicleta_id' => $this->moto->id, 'kilometraje_ingreso' => 10500, 'estado_motor' => 'BUENO', 'estado_frenos' => 'MALO', 'estado_electrico' => 'BUENO', 'diagnostico_general' => 'Frenos gastados'])->assertRedirect('/diagnosticos');
    $diag = Diagnostico::first();
    expect($diag->cita_id)->toBe($cita->id)->and($diag->empleado_id)->toBe($this->meca->empleado_id);
    expect($cita->fresh()->estado)->toBe('completada')->and($this->moto->fresh()->kilometraje)->toBe(10500);
    // km menor que el de la moto: error
    $this->actingAs($this->meca)->post('/diagnosticos', ['motocicleta_id' => $this->moto->id, 'kilometraje_ingreso' => 5, 'estado_motor' => 'B', 'estado_frenos' => 'B', 'estado_electrico' => 'B', 'diagnostico_general' => 'x'])->assertSessionHasErrors('kilometraje_ingreso');

    // CU18: cotizacion
    $serv = Servicio::first(); $rep = Repuesto::where('stock_actual', '>', 5)->first();
    $datosCot = fn ($n) => ['diagnostico_id' => $diag->id, 'nro_cotizacion' => $n, 'tipo' => 'inicial', 'fecha_emision' => now()->format('Y-m-d'),
        'servicios' => [['servicio_id' => $serv->id, 'precio_mano_obra' => 100]], 'repuestos' => [['repuesto_id' => $rep->id, 'cantidad' => 2, 'precio_unitario' => 25]]];
    $this->actingAs($this->meca)->post('/cotizaciones', $datosCot('COT-1'))->assertForbidden();
    $this->actingAs($this->jefe)->post('/cotizaciones', $datosCot('COT-1'))->assertRedirect('/cotizaciones');
    $this->actingAs($this->jefe)->post('/cotizaciones', $datosCot('COT-2'))->assertRedirect('/cotizaciones');
    $c1 = Cotizacion::where('nro_cotizacion', 'COT-1')->first(); $c2 = Cotizacion::where('nro_cotizacion', 'COT-2')->first();
    expect((float) $c1->monto_total)->toBe(150.0)->and($c2->version)->toBe(2);
    // orden desde pendiente: no
    $this->actingAs($this->jefe)->post('/ordenes-trabajo', ['cotizacion_id' => $c1->id])->assertSessionHasErrors('error');
    // cliente aprueba la suya
    $this->actingAs($this->cli)->post("/cotizaciones/{$c1->id}/estado", ['estado' => 'aprobada'])->assertRedirect();
    expect($c1->fresh()->estado)->toBe('aprobada')->and($c1->fresh()->fecha_respuesta)->not->toBeNull();
    // cliente no puede volver a cambiarla
    $this->actingAs($this->cli)->post("/cotizaciones/{$c1->id}/estado", ['estado' => 'rechazada'])->assertSessionHasErrors('error');

    // CU19: orden
    $this->actingAs($this->jefe)->post('/ordenes-trabajo', ['cotizacion_id' => $c1->id])->assertRedirect();
    $orden = OrdenTrabajo::first();
    expect($orden->estado)->toBe('activa')->and((float) $orden->monto_total)->toBe(150.0)->and($orden->km_ingreso)->toBe(10500);
    $this->actingAs($this->jefe)->post('/ordenes-trabajo', ['cotizacion_id' => $c1->id])->assertSessionHasErrors('error');
    // tarea asignada -> notifica al mecanico
    $this->actingAs($this->jefe)->post('/tareas-servicios', ['orden_trabajo_id' => $orden->id, 'empleado_id' => $this->meca->empleado_id, 'servicio_id' => $serv->id, 'descripcion' => 'Cambiar pastillas', 'precio_mano_obra' => 100])->assertRedirect();
    expect(Notificacion::where('user_id', $this->meca->id)->where('tipo', 'tarea_asignada')->exists())->toBeTrue();

    // CU20: repuestos con movimiento de inventario
    $stock = $rep->stock_actual;
    $this->actingAs($this->meca)->post("/ordenes-trabajo/{$orden->id}/repuestos", ['repuesto_id' => $rep->id, 'cantidad' => 2])->assertRedirect();
    expect($rep->fresh()->stock_actual)->toBe($stock - 2);
    $mov = MovimientoInventario::where('motivo', 'uso_orden')->first();
    expect($mov)->not->toBeNull()->and($mov->tipo)->toBe('salida')->and($mov->uso_repuesto_id)->not->toBeNull();
    $this->actingAs($this->meca)->post("/ordenes-trabajo/{$orden->id}/repuestos", ['repuesto_id' => $rep->id, 'cantidad' => 99999])->assertSessionHasErrors('error');
    expect($rep->fresh()->stock_actual)->toBe($stock - 2);

    // CU22: pagos
    $this->actingAs($this->meca)->post("/ordenes-trabajo/{$orden->id}/pagos", ['monto' => 10, 'metodo' => 'efectivo'])->assertForbidden();
    $this->actingAs($this->jefe)->post("/ordenes-trabajo/{$orden->id}/pagos", ['monto' => 100, 'metodo' => 'qr'])->assertRedirect();
    $this->actingAs($this->jefe)->post("/ordenes-trabajo/{$orden->id}/pagos", ['monto' => 100, 'metodo' => 'qr'])->assertSessionHasErrors('error');
    $this->actingAs($this->jefe)->post("/ordenes-trabajo/{$orden->id}/pagos", ['monto' => 50, 'metodo' => 'efectivo'])->assertRedirect();
    expect((float) Pago::sum('monto'))->toBe(150.0);

    // CU23: garantia al completar
    $this->actingAs($this->meca)->patch("/ordenes-trabajo/{$orden->id}/estado", ['estado' => 'completada'])->assertRedirect();
    $g = Garantia::first();
    expect($g)->not->toBeNull()->and($g->estado)->toBe('vigente')->and($g->dias)->toBe(90);
    $this->actingAs($this->meca)->patch("/ordenes-trabajo/{$orden->id}/estado", ['estado' => 'entregada'])->assertRedirect();
    expect($orden->fresh()->fecha_entrega_real)->not->toBeNull()->and(Garantia::count())->toBe(1);
    $this->actingAs($this->meca)->patch("/ordenes-trabajo/{$orden->id}/estado", ['estado' => 'activa'])->assertSessionHasErrors('error');

    // CU24: reclamo
    $this->actingAs($this->cli)->post("/garantias/{$g->id}/reclamos", ['descripcion_problema' => 'Chilla el freno', 'km' => 11000])->assertForbidden();
    $this->actingAs($this->jefe)->post("/garantias/{$g->id}/reclamos", ['descripcion_problema' => 'Chilla el freno', 'km' => 11000])->assertRedirect();
    $this->actingAs($this->jefe)->post("/garantias/{$g->id}/reclamos", ['descripcion_problema' => 'Muy lejos', 'km' => 99000])->assertSessionHasErrors('error');
    $r = ReclamoGarantia::first();
    $this->actingAs($this->jefe)->patch("/reclamos/{$r->id}/estado", ['estado' => 'aprobado'])->assertRedirect();
    expect($r->fresh()->estado)->toBe('aprobado');

    // Vistas del cliente: solo lo suyo
    $this->actingAs($this->cli)->get('/ordenes-trabajo')->assertOk()->assertSee($orden->nro_orden);
    $this->actingAs($this->cli)->get('/garantias')->assertOk()->assertSee($orden->nro_orden);
    $this->actingAs($this->cli)->get('/cotizaciones')->assertOk()->assertSee('COT-1');
    $this->actingAs($this->cli)->get('/notificaciones')->assertOk()->assertSee('Nueva');
    expect(Notificacion::where('user_id', $this->cli->id)->where('leida', false)->count())->toBe(0);
    $this->actingAs($this->jefe)->get('/reportes')->assertOk()->assertSee('150.00');
    $this->actingAs($this->jefe)->get("/ordenes-trabajo/{$orden->id}/comprobante")->assertOk()->assertSee('111AAA');
    $this->actingAs($this->admin)->get('/garantias')->assertOk()->assertSee('Editar condiciones');
});

it('el cliente no ve datos ajenos', function () {
    $citaOtro = Cita::create(['cliente_id' => $this->motoOtro->cliente_id, 'motocicleta_id' => $this->motoOtro->id, 'fecha_hora' => now()->addDay(), 'motivo' => 'MOTIVO-AJENO', 'estado' => 'programada']);
    $this->actingAs($this->cli)->get('/citas')->assertOk()->assertDontSee('MOTIVO-AJENO');
    $this->actingAs($this->cli)->get("/citas/{$citaOtro->id}")->assertNotFound();
    $this->actingAs($this->cli)->patch("/citas/{$citaOtro->id}/cancelar")->assertNotFound();
    $this->actingAs($this->cli)->get('/citas/create')->assertOk()->assertSee('111AAA')->assertDontSee('222BBB');
    $this->actingAs($this->jefe)->get('/citas')->assertSee('MOTIVO-AJENO');
    // menu del cliente
    $this->actingAs($this->cli)->get('/dashboard')->assertOk()->assertDontSee('Proveedores')->assertDontSee('Reportes')->assertSee('Mis citas');
    $this->actingAs($this->meca)->get('/dashboard')->assertOk()->assertDontSee('Reportes')->assertSee('Órdenes de trabajo');
});
