<?php

namespace App\Services;

use App\Models\Layer;
use App\Repositories\LayerRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Service para gerenciar regras de negócio de Layers
 * 
 * O Service Pattern centraliza as regras de negócio da aplicação.
 * Controllers chamam Services, e Services chamam Repositories.
 * 
 * Responsabilidades:
 * - Validar dados de negócio
 * - Processar arquivos GeoJSON
 * - Orquestrar operações complexas
 * - Logar eventos importantes
 */
class LayerService
{
    /**
     * @param LayerRepository $repository
     */
    public function __construct(
        protected LayerRepository $repository
    ) {}

    /**
     * Cria uma nova layer a partir de um arquivo GeoJSON
     * 
     * Este método:
     * 1. Valida o arquivo GeoJSON
     * 2. Extrai a geometria
     * 3. Cria a layer no banco
     * 4. Loga a operação
     * 
     * @param string $name Nome da camada
     * @param UploadedFile $geojsonFile Arquivo .geojson
     * @return Layer
     * @throws ValidationException Se o GeoJSON for inválido
     */
    public function createFromGeoJson(string $name, UploadedFile $geojsonFile): Layer
    {
        // Lê o conteúdo do arquivo
        $content = file_get_contents($geojsonFile->getRealPath());
        
        // Decodifica o JSON
        $geojson = json_decode($content, true);

        // Valida se é um JSON válido
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw ValidationException::withMessages([
                'geojson' => ['O arquivo não contém um JSON válido.']
            ]);
        }

        // Valida a estrutura do GeoJSON
        $this->validateGeoJson($geojson);

        // Extrai a geometria do GeoJSON
        $geometry = $this->extractGeometry($geojson);

        // Cria a layer no banco
        $layer = $this->repository->create([
            'name' => $name,
            'geometry' => $geometry,
        ]);

        // Loga a criação
        Log::info('Layer criada com sucesso', [
            'layer_id' => $layer->id,
            'name' => $name,
            'file_size' => $geojsonFile->getSize(),
        ]);

        return $layer;
    }

    /**
     * Atualiza uma layer existente
     * 
     * @param int $id ID da layer
     * @param string $name Novo nome
     * @param UploadedFile|null $geojsonFile Novo arquivo GeoJSON (opcional)
     * @return Layer
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws ValidationException
     */
    public function update(int $id, string $name, ?UploadedFile $geojsonFile = null): Layer
    {
        $layer = $this->repository->findByIdOrFail($id);

        $data = ['name' => $name];

        // Se foi enviado um novo arquivo GeoJSON, processa
        if ($geojsonFile) {
            $content = file_get_contents($geojsonFile->getRealPath());
            $geojson = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw ValidationException::withMessages([
                    'geojson' => ['O arquivo não contém um JSON válido.']
                ]);
            }

            $this->validateGeoJson($geojson);
            $data['geometry'] = $this->extractGeometry($geojson);
        }

        $this->repository->update($layer, $data);

        Log::info('Layer atualizada com sucesso', [
            'layer_id' => $layer->id,
            'name' => $name,
        ]);

        return $layer->fresh(); // Recarrega do banco
    }

    /**
     * Remove uma layer
     * 
     * @param int $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $layer = $this->repository->findByIdOrFail($id);
        
        $deleted = $this->repository->delete($layer);

        Log::info('Layer removida com sucesso', [
            'layer_id' => $id,
            'name' => $layer->name,
        ]);

        return $deleted ?? false;
    }

    /**
     * Valida a estrutura de um GeoJSON
     * 
     * Verifica se o GeoJSON possui os campos obrigatórios
     * e se o tipo é válido.
     * 
     * @param array $geojson
     * @throws ValidationException
     */
    protected function validateGeoJson(array $geojson): void
    {
        // Tipos válidos de GeoJSON
        $validTypes = [
            'Point',
            'LineString',
            'Polygon',
            'MultiPoint',
            'MultiLineString',
            'MultiPolygon',
            'GeometryCollection',
            'Feature',
            'FeatureCollection'
        ];

        // Verifica se tem o campo 'type'
        if (!isset($geojson['type'])) {
            throw ValidationException::withMessages([
                'geojson' => ['O GeoJSON deve conter um campo "type".']
            ]);
        }

        // Verifica se o tipo é válido
        if (!in_array($geojson['type'], $validTypes)) {
            throw ValidationException::withMessages([
                'geojson' => ["Tipo de GeoJSON inválido: {$geojson['type']}"]
            ]);
        }

        // Se for Feature ou FeatureCollection, deve ter geometry
        if ($geojson['type'] === 'Feature' && !isset($geojson['geometry'])) {
            throw ValidationException::withMessages([
                'geojson' => ['GeoJSON do tipo Feature deve conter um campo "geometry".']
            ]);
        }

        // Se for geometria básica, deve ter coordinates
        $basicGeometries = ['Point', 'LineString', 'Polygon', 'MultiPoint', 'MultiLineString', 'MultiPolygon'];
        if (in_array($geojson['type'], $basicGeometries) && !isset($geojson['coordinates'])) {
            throw ValidationException::withMessages([
                'geojson' => ['Geometria deve conter um campo "coordinates".']
            ]);
        }
    }

    /**
     * Extrai a geometria de um GeoJSON
     * 
     * Retorna a geometria em formato que o PostGIS pode processar.
     * 
     * @param array $geojson
     * @return array Geometria ou GeometryCollection
     */
    protected function extractGeometry(array $geojson): array
    {
        // Se for um FeatureCollection, extrai todas as geometrias
        if ($geojson['type'] === 'FeatureCollection') {
            $geometries = [];
            foreach ($geojson['features'] as $feature) {
                if (isset($feature['geometry'])) {
                    $geometries[] = $feature['geometry'];
                }
            }

            // Retorna como GeometryCollection
            return [
                'type' => 'GeometryCollection',
                'geometries' => $geometries
            ];
        }

        // Se for um Feature, extrai a geometria
        if ($geojson['type'] === 'Feature') {
            return $geojson['geometry'];
        }

        // Caso contrário, o próprio GeoJSON já é a geometria
        return $geojson;
    }

    /**
     * Retorna todas as layers formatadas para exibição em API
     * 
     * @return array
     */
    public function getAllForApi(): array
    {
        return $this->repository->getAllAsGeoJsonFeatureCollection();
    }

    /**
     * Busca layers por nome com paginação
     * 
     * @param string|null $search Termo de busca
     * @param int $perPage Items por página
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(?string $search = null, int $perPage = 15)
    {
        if ($search) {
            return $this->repository->searchByName($search, $perPage);
        }

        return $this->repository->paginate($perPage);
    }
}
