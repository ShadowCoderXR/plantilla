<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('admin.dashboard');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/documento/{id}/descargar/{unicaVez?}', [AdminController::class, 'documentoDescargar'])->name('documento.descargar');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/administrador/{id}', [AdminController::class, 'administrador'])->name('administrador');
    Route::get('/cliente/{id}', [AdminController::class, 'cliente'])->name('cliente');
    Route::get('/proveedor/{idProveedor}/{idCliente}/{año}/{tipo}', [AdminController::class, 'proveedor'])->name('proveedor');
    Route::get('/documento/{id}/{unicaVez?}', [AdminController::class, 'documento'])->name('documento');
    Route::post('/documento/{id}/{unicaVez?}', [AdminController::class, 'documentoGuardar'])->name('documento.guardar');
    Route::get('/cliente/{id}/proveedores', [AdminController::class, 'proveedores'])->name('cliente.proveedores');
    Route::delete('/documento/archivo/eliminar', [AdminController::class, 'eliminarArchivo'])->name('documento.archivo.eliminar');
});

Route::post('/descargar-documentos', [AdminController::class, 'generarZip'])->name('documentos.zip');

