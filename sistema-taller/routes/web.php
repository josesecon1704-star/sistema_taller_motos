<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServicioController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


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
    Route::resource('servicios', ServicioController::class)
        ->except(['show', 'destroy'])
        ->parameters(['servicios' => 'servicio']);

    Route::patch('servicios/{servicio}/estado', [ServicioController::class, 'cambiarEstado'])
        ->name('servicios.estado');
});


require __DIR__ . '/auth.php';
