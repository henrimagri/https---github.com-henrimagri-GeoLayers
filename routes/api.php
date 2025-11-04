<?php

use App\Http\Controllers\Api\LayerController;
use Illuminate\Support\Facades\Route;

/**
 * Rotas da API REST
 * 
 * Todas as rotas aqui são prefixadas com /api automaticamente.
 * Não requerem autenticação - são públicas para consumo do mapa.
 */

/**
 * Layers (Camadas) API
 * 
 * Endpoints para consumir dados de camadas geográficas.
 */
Route::prefix('layers')->name('api.layers.')->group(function () {
    
    // Lista todas as camadas como GeoJSON FeatureCollection
    // GET /api/layers
    Route::get('/', [LayerController::class, 'index'])->name('index');
    
    // Retorna uma camada específica
    // GET /api/layers/{id}
    Route::get('/{id}', [LayerController::class, 'show'])->name('show');
    
    // Busca camadas que contenham um ponto
    // GET /api/layers/contains?longitude=-46.6333&latitude=-23.5505
    Route::get('/contains', [LayerController::class, 'containsPoint'])->name('contains');
    
    // Busca camadas dentro de uma distância de um ponto
    // GET /api/layers/within?longitude=-46.6333&latitude=-23.5505&distance=1000
    Route::get('/within', [LayerController::class, 'withinDistance'])->name('within');
});
