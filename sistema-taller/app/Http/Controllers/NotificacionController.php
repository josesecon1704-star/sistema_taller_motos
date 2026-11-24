<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificacionController extends Controller
{
    public function index()
    {
        $notifications = collect();

        // 1. Repuestos con stock bajo (comparando stock actual contra el stock mínimo configurado)
        try {
            $lowStock = DB::table('repuestos')
                ->whereColumn('stock_actual', '<=', 'stock_minimo')
                ->get()
                ->map(function ($item) {
                    return [
                        'tipo' => 'Stock Bajo',
                        'titulo' => 'Repuesto con poco stock: ' . ($item->nombre ?? 'Sin nombre'),
                        'mensaje' => 'Quedan solo ' . ($item->stock_actual ?? 0) . ' unidades disponibles.',
                        'color' => 'yellow',
                        'fecha' => now()->format('Y-m-d H:i')
                    ];
                });
            $notifications = $notifications->merge($lowStock);
        } catch (\Exception $e) {}

        // 2. Servicios Inactivos (Atrapa cualquier variante de desactivado: texto o número)
        try {
            $inactiveServices = DB::table('servicios')
                ->where(function($query) {
                    $query->where('estado', 'Inactivo')
                          ->orWhere('estado', 'inactivo')
                          ->orWhere('estado', 'INACTIVO')
                          ->orWhere('estado', 0)
                          ->orWhere('estado', '0')
                          ->orWhere('estado', false);
                })
                ->get()
                ->map(function ($item) {
                    return [
                        'tipo' => 'Servicio Inactivo',
                        'titulo' => 'Servicio inactivo: ' . ($item->nombre ?? 'Servicio'),
                        'mensaje' => 'Este servicio se encuentra desactivado actualmente.',
                        'color' => 'blue',
                        'fecha' => now()->format('Y-m-d H:i')
                    ];
                });
            $notifications = $notifications->merge($inactiveServices);
        } catch (\Exception $e) {}

        return view('notifications.index', compact('notifications'));
    }
}