<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Model Layer - Representa uma camada geográfica
 * 
 * Este model gerencia camadas (layers) que contêm dados geográficos.
 * Cada layer possui um nome e uma geometria (armazenada como PostGIS GEOMETRY).
 * 
 * @property int $id
 * @property string $name Nome da camada
 * @property string $geometry Geometria em formato WKT (Well-Known Text) ou GeoJSON
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Layer extends Model
{
    use HasFactory;

    /**
     * Nome da tabela no banco de dados
     */
    protected $table = 'layers';

    /**
     * Campos que podem ser preenchidos em massa (mass assignment)
     * 
     * Permite usar Layer::create(['name' => '...', 'geometry' => '...'])
     */
    protected $fillable = [
        'name',
        'geometry',
    ];

    /**
     * Converte a geometria para GeoJSON ao serializar para JSON
     * 
     * Este accessor é chamado automaticamente quando o model é convertido para JSON.
     * Usa a função ST_AsGeoJSON do PostGIS para converter a geometria interna
     * para o formato GeoJSON padrão.
     * 
     * @return array|null Array com estrutura GeoJSON ou null se não houver geometria
     */
    public function getGeometryGeoJsonAttribute(): ?array
    {
        if (!$this->geometry) {
            return null;
        }

        // Executa query para converter geometry para GeoJSON
        // ST_AsGeoJSON retorna uma string JSON com a geometria
        $result = DB::selectOne(
            'SELECT ST_AsGeoJSON(geometry) as geojson FROM layers WHERE id = ?',
            [$this->id]
        );

        return $result ? json_decode($result->geojson, true) : null;
    }

    /**
     * Converte GeoJSON para formato PostGIS antes de salvar
     * 
     * Este mutator permite que você defina a geometria usando GeoJSON:
     * $layer->geometry = ['type' => 'Point', 'coordinates' => [-46.6333, -23.5505]];
     * 
     * @param mixed $value Pode ser GeoJSON (array), WKT (string) ou null
     */
    public function setGeometryAttribute($value): void
    {
        if (is_null($value)) {
            $this->attributes['geometry'] = null;
            return;
        }

        // Se for um array, assume que é GeoJSON
        if (is_array($value)) {
            $value = json_encode($value);
        }

        // Se for string e parecer JSON, usa ST_GeomFromGeoJSON
        if (is_string($value) && (str_starts_with($value, '{') || str_starts_with($value, '['))) {
            // ST_GeomFromGeoJSON converte GeoJSON para GEOMETRY do PostGIS
            // SRID 4326 = WGS84 (coordenadas geográficas padrão)
            $this->attributes['geometry'] = DB::raw("ST_GeomFromGeoJSON('$value')");
        } else {
            // Caso contrário, assume que é WKT (Well-Known Text)
            // Ex: "POINT(-46.6333 -23.5505)"
            $this->attributes['geometry'] = DB::raw("ST_GeomFromText('$value', 4326)");
        }
    }

    /**
     * Retorna a geometria completa como GeoJSON
     * 
     * Útil para exportar a camada completa para uso em mapas.
     * 
     * @return array GeoJSON Feature com propriedades e geometria
     */
    public function toGeoJson(): array
    {
        return [
            'type' => 'Feature',
            'id' => $this->id,
            'properties' => [
                'name' => $this->name,
                'created_at' => $this->created_at?->toIso8601String(),
                'updated_at' => $this->updated_at?->toIso8601String(),
            ],
            'geometry' => $this->geometry_geo_json,
        ];
    }

    /**
     * Scope para buscar layers que contenham um ponto específico
     * 
     * Exemplo: Layer::containsPoint(-46.6333, -23.5505)->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $longitude
     * @param float $latitude
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeContainsPoint($query, float $longitude, float $latitude)
    {
        return $query->whereRaw(
            'ST_Contains(geometry, ST_SetSRID(ST_MakePoint(?, ?), 4326))',
            [$longitude, $latitude]
        );
    }

    /**
     * Scope para buscar layers dentro de uma distância de um ponto
     * 
     * Exemplo: Layer::withinDistance(-46.6333, -23.5505, 1000)->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $longitude
     * @param float $latitude
     * @param float $distance Distância em metros
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithinDistance($query, float $longitude, float $latitude, float $distance)
    {
        return $query->whereRaw(
            'ST_DWithin(geography(geometry), geography(ST_SetSRID(ST_MakePoint(?, ?), 4326)), ?)',
            [$longitude, $latitude, $distance]
        );
    }
}
