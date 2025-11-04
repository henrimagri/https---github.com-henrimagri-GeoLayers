<?php

namespace App\Repositories;

use App\Models\Layer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Repository para acesso a dados de Layers
 * 
 * O Repository Pattern isola a lógica de acesso ao banco de dados,
 * facilitando testes e manutenção. Toda interação com o banco deve
 * passar por este repository.
 * 
 * Responsabilidades:
 * - Executar queries no banco
 * - Retornar objetos Model ou Collections
 * - NÃO contém regras de negócio (isso fica no Service)
 */
class LayerRepository
{
    /**
     * Retorna todas as layers
     * 
     * @return Collection<Layer>
     */
    public function all(): Collection
    {
        return Layer::orderBy('created_at', 'desc')->get();
    }

    /**
     * Retorna layers com paginação
     * 
     * @param int $perPage Número de items por página
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Layer::orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Busca layers por nome (busca parcial)
     * 
     * Usa ILIKE para busca case-insensitive no PostgreSQL
     * 
     * @param string $name Nome ou parte do nome
     * @param int $perPage Número de items por página
     * @return LengthAwarePaginator
     */
    public function searchByName(string $name, int $perPage = 15): LengthAwarePaginator
    {
        return Layer::where('name', 'ILIKE', "%{$name}%")
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Busca uma layer por ID
     * 
     * @param int $id
     * @return Layer|null
     */
    public function findById(int $id): ?Layer
    {
        return Layer::find($id);
    }

    /**
     * Busca uma layer por ID ou lança exceção
     * 
     * @param int $id
     * @return Layer
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByIdOrFail(int $id): Layer
    {
        return Layer::findOrFail($id);
    }

    /**
     * Cria uma nova layer
     * 
     * @param array $data Dados da layer ['name' => '...', 'geometry' => '...']
     * @return Layer
     */
    public function create(array $data): Layer
    {
        return Layer::create($data);
    }

    /**
     * Atualiza uma layer existente
     * 
     * @param Layer $layer
     * @param array $data Dados para atualizar
     * @return bool
     */
    public function update(Layer $layer, array $data): bool
    {
        return $layer->update($data);
    }

    /**
     * Remove uma layer
     * 
     * @param Layer $layer
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Layer $layer): ?bool
    {
        return $layer->delete();
    }

    /**
     * Retorna todas as layers como GeoJSON FeatureCollection
     * 
     * Formato compatível com bibliotecas de mapas (Leaflet, ArcGIS, etc)
     * 
     * @return array GeoJSON FeatureCollection
     */
    public function getAllAsGeoJsonFeatureCollection(): array
    {
        $layers = $this->all();

        return [
            'type' => 'FeatureCollection',
            'features' => $layers->map(fn(Layer $layer) => $layer->toGeoJson())->toArray(),
        ];
    }

    /**
     * Busca layers que contenham um ponto específico
     * 
     * @param float $longitude
     * @param float $latitude
     * @return Collection<Layer>
     */
    public function findContainingPoint(float $longitude, float $latitude): Collection
    {
        return Layer::containsPoint($longitude, $latitude)->get();
    }

    /**
     * Busca layers dentro de uma distância de um ponto
     * 
     * @param float $longitude
     * @param float $latitude
     * @param float $distance Distância em metros
     * @return Collection<Layer>
     */
    public function findWithinDistance(float $longitude, float $latitude, float $distance): Collection
    {
        return Layer::withinDistance($longitude, $latitude, $distance)->get();
    }

    /**
     * Conta o total de layers cadastradas
     * 
     * @return int
     */
    public function count(): int
    {
        return Layer::count();
    }
}
