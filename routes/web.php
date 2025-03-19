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
    Route::get('/documento/{status}', [AdminController::class, 'documento'])->name('documento');
});
