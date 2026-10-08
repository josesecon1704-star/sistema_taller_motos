<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;

class RolController extends Controller
{
    //lista los elementos de una tabla
    public function index()
    {
        return "rol r";
    }


    //gardar datos de la tabla CREAR
    public function store(Request $request)
    {
        //
    }

    //muestra solo un elemento de la tabla MOSTRAR UN ELEMENTO
    public function show($id)
    {
        return "show";
    }


    //permite modificar un elemento MODIFICAR
    public function update(Request $request, Rol $rol)
    {
        //
    }

    
    //permite eliminar un elemento de una tabla ELIMINAR
    public function destroy($id)
    {
        //
    }
}
