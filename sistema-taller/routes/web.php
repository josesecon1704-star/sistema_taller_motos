<?php

use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RolController;
use Illuminate\Support\Facades\Route;


Route::prefix("rol")->controller(RolController::class)->name("rol.")->group(function(){
    Route::get('/','index')->name("listar");
    Route::post('/','store')->name("guardar");
    Route::post('/delete','delete')->name("modificar");
    Route::post('/update','update')->name("eliminar");
    Route::get('/{id}','show')->name("mostrar");
    
});


Route::prefix("proveedor")->controller(ProveedorController::class)->name("proveedor.")->group(function(){
    Route::get('/','index')->name("listar");
    Route::post('/','store')->name("guardar");
    Route::post('/delete','delete')->name("modificar");
    Route::post('/update','update')->name("eliminar");
    Route::get('/{id}','show')->name("mostrar");
    
});