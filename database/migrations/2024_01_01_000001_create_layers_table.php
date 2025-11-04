<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration para criar a tabela 'layers'
 * 
 * Esta tabela armazena camadas geográficas (layers) com suas geometrias.
 * Cada layer representa um conjunto de dados geográficos (pontos, linhas, polígonos)
 * que serão exibidos no mapa.
 */
return new class extends Migration
{
    /**
     * Executa a migration.
     * 
     * Cria a tabela 'layers' com os seguintes campos:
     * - id: identificador único auto-incremental
     * - name: nome descritivo da camada (max 100 caracteres)
     * - geometry: campo espacial PostGIS para armazenar geometrias (GEOMETRY type)
     * - created_at e updated_at: timestamps automáticos do Laravel
     */
    public function up(): void
    {
        Schema::create('layers', function (Blueprint $table) {
            // ID auto-incremental (chave primária)
            $table->id();
            
            // Nome da camada (obrigatório, máximo 100 caracteres)
            $table->string('name', 100);
            
            // Timestamps padrão do Laravel (created_at, updated_at)
            $table->timestamps();
        });

        // Adiciona o campo geometry usando SQL nativo do PostGIS
        // GEOMETRY é um tipo genérico que aceita qualquer tipo de geometria:
        // - POINT: pontos (latitude/longitude)
        // - LINESTRING: linhas
        // - POLYGON: polígonos
        // - MULTIPOINT, MULTILINESTRING, MULTIPOLYGON: coleções
        // - GEOMETRYCOLLECTION: mistura de tipos
        // SRID 4326 = WGS84 (sistema de coordenadas geográficas padrão usado por GPS)
        DB::statement('ALTER TABLE layers ADD COLUMN geometry GEOMETRY(GEOMETRY, 4326)');

        // Cria um índice espacial GIST sobre o campo geometry
        // GIST (Generalized Search Tree) otimiza consultas espaciais como:
        // - ST_Intersects (verificar se geometrias se cruzam)
        // - ST_Contains (verificar se uma geometria contém outra)
        // - ST_DWithin (encontrar geometrias dentro de uma distância)
        // Isso melhora drasticamente a performance de queries geográficas
        DB::statement('CREATE INDEX layers_geometry_idx ON layers USING GIST (geometry)');
    }

    /**
     * Reverte a migration.
     * 
     * Remove a tabela 'layers' e todos os seus dados.
     * O índice espacial é removido automaticamente ao dropar a tabela.
     */
    public function down(): void
    {
        Schema::dropIfExists('layers');
    }
};
