<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AutenticacionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('inicio-sesion', [AutenticacionController::class, 'formulario'])->name('inicio.sesion')->middleware('guest');
Route::post('inicio-sesion', [AutenticacionController::class, 'iniciarSesion'])->name('iniciar.sesion');
Route::post('cerrar-sesion', [AutenticacionController::class, 'cerrarSesion'])->name('cerrar.sesion');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/documento/{id}/descargar/{unicaVez?}', [AdminController::class, 'documentoDescargar'])->name('documento.descargar');
    Route::delete('/documento/archivo/eliminar', [AdminController::class, 'eliminarArchivo'])->name('documento.archivo.eliminar');

    Route::get('/documento/{id}/{unicaVez?}', [AdminController::class, 'documento'])->name('documento');
    Route::post('/documento/{id}/{unicaVez?}', [AdminController::class, 'documentoGuardar'])->name('documento.guardar');

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/administrador/{id}', [AdminController::class, 'administrador'])->name('administrador');

    Route::get('/cliente/{id}', [AdminController::class, 'cliente'])->name('cliente');
    Route::get('/cliente/{id}/proveedores', [AdminController::class, 'proveedores'])->name('cliente.proveedores');

    Route::get('/proveedor/{idProveedor}/{idCliente}/{año}/{tipo}', [AdminController::class, 'proveedor'])->name('proveedor');

    Route::post('/documentos/zip', [AdminController::class, 'generarZip'])->name('documentos.zip');
    Route::get('/documentos/zip-progreso/{nombre}', [AdminController::class, 'zipProgreso'])->name('documentos.zip.progreso');
    Route::get('/documentos/descargar/{nombre}', [AdminController::class, 'descargarZip'])->name('documentos.zip.descargar');
    Route::get('/documentos/esperando/{nombre}', [AdminController::class, 'esperandoVista'])->name('documentos.zip.esperando');


});

Route::post('/descargar-documentos', [AdminController::class, 'generarZip'])->name('documentos.zip');
