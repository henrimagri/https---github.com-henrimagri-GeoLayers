<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migration para habilitar a extensão PostGIS no PostgreSQL
 * 
 * PostGIS adiciona suporte para objetos geográficos ao PostgreSQL,
 * permitindo armazenar e consultar dados espaciais (pontos, linhas, polígonos, etc).
 */
return new class extends Migration
{
    /**
     * Executa a migration.
     * 
     * Habilita a extensão PostGIS no banco de dados.
     * Esta extensão deve estar instalada no PostgreSQL antes de executar.
     */
    public function up(): void
    {
        // Habilita a extensão PostGIS se ainda não estiver habilitada
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');
    }

    /**
     * Reverte a migration.
     * 
     * Remove a extensão PostGIS do banco de dados.
     * ATENÇÃO: Isso removerá TODOS os tipos de dados espaciais!
     */
    public function down(): void
    {
        DB::statement('DROP EXTENSION IF EXISTS postgis');
    }
};
