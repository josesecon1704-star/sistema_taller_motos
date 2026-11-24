<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $vista = match ($request->user()->rol->nombre) {
            'Administrador'  => 'dashboard.admin',
            'Jefe de Taller' => 'dashboard.jefe',
            'Mecanico'       => 'dashboard.mecanico',
            default          => 'dashboard.cliente',
        };

        return view($vista);
    }
}
