<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('admin.dashboard');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/administrador/{id}', [AdminController::class, 'administrador'])->name('administrador');
    Route::get('/cliente/{id}', [AdminController::class, 'cliente'])->name('cliente');
    Route::get('/proveedor/{id}/{año}', [AdminController::class, 'proveedor'])->name('proveedor');
    Route::get('/documento/{id}', [AdminController::class, 'documento'])->name('documento');
    Route::post('/documento/{id}', [AdminController::class, 'documentoGuardar'])->name('documento.guardar');
    Route::get('/documento/{id}/descargar', [AdminController::class, 'documentoDescargar'])->name('documento.descargar');
    Route::get('/cliente/{id}/proveedores', [AdminController::class, 'proveedores'])->name('cliente.proveedores');
});

Route::post('/descargar-documentos', [AdminController::class, 'generarZip'])->name('documentos.zip');

