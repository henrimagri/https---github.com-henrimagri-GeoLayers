<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\LayerRepository;
use Illuminate\Http\JsonResponse;

/**
 * API Controller para Layers
 * 
 * Fornece endpoints REST para consumo de dados de layers.
 * Estes endpoints são usados pela página do mapa para carregar
 * as camadas geográficas.
 * 
 * Rotas (prefixo /api):
 * - GET /api/layers - Lista todas as layers como GeoJSON FeatureCollection
 * - GET /api/layers/{id} - Retorna uma layer específica como GeoJSON Feature
 */
class LayerController extends Controller
{
    /**
     * @param LayerRepository $repository
     */
    public function __construct(
        protected LayerRepository $repository
    ) {}

    /**
     * Lista todas as layers como GeoJSON FeatureCollection
     * 
     * Retorna um objeto GeoJSON FeatureCollection contendo todas as layers.
     * Este formato é compatível com ArcGIS Maps SDK, Leaflet, Mapbox, etc.
     * 
     * Exemplo de resposta:
     * {
     *   "type": "FeatureCollection",
     *   "features": [
     *     {
     *       "type": "Feature",
     *       "id": 1,
     *       "properties": { "name": "Pontos de Interesse" },
     *       "geometry": { "type": "Point", "coordinates": [-46.6333, -23.5505] }
     *     }
     *   ]
     * }
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $featureCollection = $this->repository->getAllAsGeoJsonFeatureCollection();

        return response()->json($featureCollection);
    }

    /**
     * Retorna uma layer específica como GeoJSON Feature
     * 
     * @param int $id ID da layer
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $layer = $this->repository->findById($id);

        if (!$layer) {
            return response()->json([
                'error' => 'Layer não encontrada',
                'message' => "Não existe layer com ID {$id}"
            ], 404);
        }

        return response()->json($layer->toGeoJson());
    }

    /**
     * Endpoint para buscar layers que contenham um ponto
     * 
     * Útil para funcionalidades de "clique no mapa" ou busca por localização.
     * 
     * Query params:
     * - longitude (obrigatório)
     * - latitude (obrigatório)
     * 
     * Exemplo: GET /api/layers/contains?longitude=-46.6333&latitude=-23.5505
     * 
     * @return JsonResponse
     */
    public function containsPoint(): JsonResponse
    {
        $longitude = request('longitude');
        $latitude = request('latitude');

        if (!$longitude || !$latitude) {
            return response()->json([
                'error' => 'Parâmetros inválidos',
                'message' => 'Os parâmetros longitude e latitude são obrigatórios'
            ], 400);
        }

        $layers = $this->repository->findContainingPoint(
            floatval($longitude),
            floatval($latitude)
        );

        $features = $layers->map(fn($layer) => $layer->toGeoJson());

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    /**
     * Endpoint para buscar layers dentro de uma distância de um ponto
     * 
     * Query params:
     * - longitude (obrigatório)
     * - latitude (obrigatório)
     * - distance (obrigatório, em metros)
     * 
     * Exemplo: GET /api/layers/within?longitude=-46.6333&latitude=-23.5505&distance=1000
     * 
     * @return JsonResponse
     */
    public function withinDistance(): JsonResponse
    {
        $longitude = request('longitude');
        $latitude = request('latitude');
        $distance = request('distance');

        if (!$longitude || !$latitude || !$distance) {
            return response()->json([
                'error' => 'Parâmetros inválidos',
                'message' => 'Os parâmetros longitude, latitude e distance são obrigatórios'
            ], 400);
        }

        $layers = $this->repository->findWithinDistance(
            floatval($longitude),
            floatval($latitude),
            floatval($distance)
        );

        $features = $layers->map(fn($layer) => $layer->toGeoJson());

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}
