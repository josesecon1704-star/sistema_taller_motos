<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\TareaServicio;
use Illuminate\Http\Request;

class TareaServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'orden_trabajo_id' => 'required|exists:orden_trabajos,id',
            'empleado_id' => 'required|exists:empleados,id',
            'descripcion' => 'required|string|max:255',
            'precio_mano_obra' => 'nullable|numeric',
        ]);

        // Obtener un servicio por defecto para cumplir con la restricción de la base de datos
        $servicioDefault = Servicio::first();

        // Instanciación directa para evitar bloqueos por mass assignment ($fillable)
        $tarea = new TareaServicio();
        $tarea->orden_trabajo_id = $request->orden_trabajo_id;
        $tarea->empleado_id = $request->empleado_id;
        $tarea->servicio_id = $request->servicio_id ?? ($servicioDefault ? $servicioDefault->id : 1);
        $tarea->descripcion = $request->descripcion;
        $tarea->precio_mano_obra = $request->precio_mano_obra ?? 0.00;
        $tarea->estado = 'PENDIENTE';
        $tarea->fecha_inicio = now();
        $tarea->save();

        return redirect()->back()
            ->with('success', 'Tarea asignada al mecánico correctamente.')
            ->with('abrir_orden_id', $tarea->orden_trabajo_id)
            ->with('abrir_tab', 'tareas');
    }

    /**
     * Cambiar el estado de la subtarea (PENDIENTE -> EN PROCESO -> COMPLETADA).
     */
    public function cambiarEstado(Request $request, TareaServicio $tareaServicio)
    {
        $validated = $request->validate([
            'estado' => 'required|string',
        ]);

        $normalizado = strtoupper(trim($validated['estado']));
        $permitidos = ['PENDIENTE', 'EN PROCESO', 'COMPLETADA'];

        if (!in_array($normalizado, $permitidos, true)) {
            return redirect()->back()
                ->withErrors(['error' => 'Estado de tarea no válido.'])
                ->with('abrir_orden_id', $tareaServicio->orden_trabajo_id)
                ->with('abrir_tab', 'tareas');
        }

        $tareaServicio->estado = $normalizado;

        if ($normalizado === 'EN PROCESO' && !$tareaServicio->fecha_inicio) {
            $tareaServicio->fecha_inicio = now();
        }

        if ($normalizado === 'COMPLETADA') {
            $tareaServicio->fecha_fin = now();
        }

        $tareaServicio->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'id' => $tareaServicio->id,
                'estado' => $tareaServicio->estado,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Estado de la tarea actualizado correctamente.')
            ->with('abrir_orden_id', $tareaServicio->orden_trabajo_id)
            ->with('abrir_tab', 'tareas');
    }

    /**
     * Display the specified resource.
     */
    public function show(TareaServicio $tareaServicio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TareaServicio $tareaServicio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TareaServicio $tareaServicio)
    {
        return $this->cambiarEstado($request, $tareaServicio);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TareaServicio $tareaServicio)
    {
        $ordenId = $tareaServicio->orden_trabajo_id;
        $tareaServicio->delete();

        return redirect()->back()
            ->with('success', 'Tarea eliminada correctamente.')
            ->with('abrir_orden_id', $ordenId)
            ->with('abrir_tab', 'tareas');
    }
}