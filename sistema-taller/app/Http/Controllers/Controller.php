<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;

/**
 * Laravel solo ejecuta el método estático middleware() de un controlador
 * si el controlador implementa HasMiddleware. Al implementarlo aquí, todos
 * los controladores que definen middleware() aplican sus permisos
 * (permiso:modulo,accion). Los que no lo definen no tienen restricciones extra.
 */
abstract class Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [];
    }
}
