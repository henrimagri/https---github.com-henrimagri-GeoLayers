<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PainelController;
use Illuminate\Support\Facades\Route;

// Página inicial - mapa público
Route::get('/', function () {
    return view('mapa');
})->name('home');

// Dashboard - redireciona para painel de camadas
Route::get('/dashboard', function () {
    return redirect()->route('painel.layers.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Painel administrativo de camadas (protegido por autenticação)
Route::middleware('auth')->group(function () {
    Route::resource('painel/layers', PainelController::class)->names([
        'index' => 'painel.layers.index',
        'create' => 'painel.layers.create',
        'store' => 'painel.layers.store',
        'edit' => 'painel.layers.edit',
        'update' => 'painel.layers.update',
        'destroy' => 'painel.layers.destroy',
    ]);
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
