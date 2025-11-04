<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# 🗺️ GeoLayers Manager

Uma aplicação web completa para gerenciar e visualizar camadas geográficas utilizando Laravel, PostgreSQL com PostGIS e ArcGIS Maps SDK.

## 📋 Sobre o Projeto

Este projeto permite:

- ✅ **Gerenciar camadas geográficas** através de um painel administrativo protegido
- ✅ **Upload de arquivos GeoJSON** com validação automática
- ✅ **Visualização interativa** de todas as camadas em um mapa ArcGIS
- ✅ **Armazenamento otimizado** com PostGIS e índices espaciais
- ✅ **API REST** para consumo de dados geográficos

## 🎯 Tecnologias Utilizadas

| Categoria | Tecnologia |
|-----------|-----------|
| **Backend** | Laravel 12 (PHP 8.2+) |
| **Banco de Dados** | PostgreSQL 14+ com PostGIS 3.4+ |
| **Frontend** | Blade Templates + Tailwind CSS |
| **Mapas** | ArcGIS Maps SDK for JavaScript 4.31 |
| **Autenticação** | Laravel Breeze |
| **Arquitetura** | MVC + Repository Pattern + Service Layer |

## 🚀 Instalação

### Pré-requisitos

1. **PHP 8.2+** com extensões:
   - `pdo_pgsql`
   - `pgsql`
   - `mbstring`
   - `fileinfo`

2. **Composer** (https://getcomposer.org/)

3. **Node.js e NPM** (https://nodejs.org/)

4. **PostgreSQL 14+ com PostGIS 3.4+**

#### Instalar PostgreSQL + PostGIS

**Via Docker (Recomendado):**
```bash
docker run --name geolayers-db \
  -e POSTGRES_DB=geolayers \
  -e POSTGRES_USER=postgres \
  -e POSTGRES_PASSWORD=postgres \
  -p 5432:5432 \
  -d postgis/postgis:16-3.4
```

**Via PostgreSQL instalado (Windows):**
1. Baixe PostgreSQL: https://www.postgresql.org/download/windows/
2. Durante instalação, marque "Stack Builder"
3. No Stack Builder, instale PostGIS

### Passo a Passo

1. **Clone o repositório**
```bash
git clone <url-do-repositorio>
cd projeto
```

2. **Instale dependências do PHP**
```bash
composer install
```

3. **Configure o arquivo .env**

O arquivo `.env` já está configurado. Verifique se as credenciais do PostgreSQL estão corretas:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=geolayers
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

4. **Crie o banco de dados e habilite PostGIS**

```bash
# Via psql
psql -U postgres -c "CREATE DATABASE geolayers;"
psql -U postgres -d geolayers -c "CREATE EXTENSION postgis;"

# Ou via Docker
docker exec -it geolayers-db psql -U postgres -c "CREATE DATABASE geolayers;"
docker exec -it geolayers-db psql -U postgres -d geolayers -c "CREATE EXTENSION postgis;"
```

5. **Instale Laravel Breeze (autenticação)**

```bash
php artisan breeze:install blade
```

Selecione:
- Stack: `blade`
- Dark mode: No (ou Yes, como preferir)
- Testing: No (ou Pest, como preferir)

6. **Execute as migrations**

```bash
php artisan migrate
```

7. **Crie um usuário administrador**

```bash
php artisan db:seed --class=AdminUserSeeder
```

**Credenciais:**
- Email: `admin@geolayers.com`
- Senha: `password`

8. **Instale dependências do NPM**

```bash
npm install
```

9. **Compile os assets**

```bash
# Para produção
npm run build

# Para desenvolvimento (com hot reload)
npm run dev
```

10. **Inicie o servidor**

```bash
php artisan serve
```

## 🌐 Acessar a Aplicação

- **Página Inicial (Mapa):** http://localhost:8000
- **Login:** http://localhost:8000/login
- **Painel Administrativo:** http://localhost:8000/painel/layers

## 📚 Estrutura do Projeto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   └── LayerController.php      # API REST para layers
│   │   └── PainelController.php         # CRUD do painel admin
├── Models/
│   └── Layer.php                        # Model com suporte PostGIS
├── Repositories/
│   └── LayerRepository.php              # Camada de acesso a dados
└── Services/
    └── LayerService.php                 # Regras de negócio

database/
├── migrations/
│   ├── 2024_01_01_000000_enable_postgis_extension.php
│   └── 2024_01_01_000001_create_layers_table.php
└── seeders/
    └── AdminUserSeeder.php

resources/
└── views/
    ├── components/
    │   └── painel-layout.blade.php      # Layout do painel
    ├── painel/
    │   └── layers/
    │       ├── index.blade.php          # Lista de layers
    │       ├── create.blade.php         # Criar layer
    │       └── edit.blade.php           # Editar layer
    └── welcome.blade.php                # Mapa ArcGIS

routes/
├── api.php                              # Rotas da API REST
└── web.php                              # Rotas web
```

## 🗺️ Usando a Aplicação

### Upload de GeoJSON

1. Faça login: http://localhost:8000/login
2. Acesse: http://localhost:8000/painel/layers
3. Clique em "Nova Camada"
4. Preencha o nome e faça upload do arquivo `.geojson`
5. Clique em "Criar Camada"

**Formatos GeoJSON suportados:**
- `Point` - Pontos únicos
- `LineString` - Linhas
- `Polygon` - Polígonos
- `MultiPoint`, `MultiLineString`, `MultiPolygon` - Coleções
- `Feature` - Feature única com propriedades
- `FeatureCollection` - Múltiplas features

**Onde criar GeoJSON:**
- https://geojson.io - Editor online visual
- QGIS - Software GIS desktop (exportar para GeoJSON)

### Visualizar no Mapa

1. Acesse: http://localhost:8000
2. Todas as camadas cadastradas serão exibidas automaticamente
3. Use o painel lateral para ligar/desligar camadas
4. Clique nas geometrias para ver informações

## 🔌 API REST

### Endpoints Disponíveis

#### Listar todas as layers (GeoJSON FeatureCollection)
```http
GET /api/layers
```

**Resposta:**
```json
{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "id": 1,
      "properties": {
        "name": "Pontos de Interesse",
        "created_at": "2024-01-01T00:00:00Z"
      },
      "geometry": {
        "type": "Point",
        "coordinates": [-46.6333, -23.5505]
      }
    }
  ]
}
```

#### Obter uma layer específica
```http
GET /api/layers/{id}
```

#### Buscar layers que contenham um ponto
```http
GET /api/layers/contains?longitude=-46.6333&latitude=-23.5505
```

#### Buscar layers dentro de uma distância
```http
GET /api/layers/within?longitude=-46.6333&latitude=-23.5505&distance=1000
```
*Distância em metros*

## 🏗️ Arquitetura

O projeto segue boas práticas de arquitetura em camadas:

### Controller → Service → Repository → Model

1. **Controllers** - Recebem requisições e retornam respostas
2. **Services** - Contém regras de negócio (validação de GeoJSON, processamento)
3. **Repositories** - Acesso ao banco de dados (queries)
4. **Models** - Representação de dados (Eloquent ORM)

**Benefícios:**
- ✅ Código mais testável
- ✅ Separação de responsabilidades
- ✅ Facilita manutenção
- ✅ Reutilização de código

## 🗃️ Banco de Dados

### Tabela `layers`

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | BIGINT | ID auto-incremental |
| `name` | VARCHAR(100) | Nome da camada |
| `geometry` | GEOMETRY(GEOMETRY, 4326) | Geometria PostGIS |
| `created_at` | TIMESTAMP | Data de criação |
| `updated_at` | TIMESTAMP | Data de atualização |

**Índices:**
- `PRIMARY KEY` em `id`
- `GIST INDEX` em `geometry` (otimização de queries espaciais)

**SRID 4326** = WGS84 (Sistema de coordenadas geográficas padrão usado por GPS)

## 🧪 Testes

Para executar testes (se configurado):
```bash
php artisan test
```

## 🐛 Troubleshooting

### Erro: "could not find driver"
- Instale a extensão `pdo_pgsql` do PHP

### Erro: "PostGIS extension not found"
```sql
-- Execute no PostgreSQL:
\c geolayers
CREATE EXTENSION postgis;
```

### Erro de permissão no storage (Linux/Mac)
```bash
chmod -R 775 storage bootstrap/cache
```

### Erro de permissão no storage (Windows)
```powershell
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T
```

### Mapa não carrega camadas
- Verifique se o servidor está rodando: `php artisan serve`
- Abra o console do navegador (F12) e veja erros
- Verifique se a API está respondendo: http://localhost:8000/api/layers

## 📖 Documentação Adicional

- [Laravel Documentation](https://laravel.com/docs)
- [PostGIS Documentation](https://postgis.net/documentation/)
- [ArcGIS Maps SDK for JavaScript](https://developers.arcgis.com/javascript/latest/)
- [GeoJSON Specification](https://geojson.org/)

## 🤝 Contribuindo

Contribuições são bem-vindas! Por favor:

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'Adiciona MinhaFeature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto é open-source sob a licença MIT.

## 👨‍💻 Autor

Desenvolvido com ❤️ seguindo boas práticas de código limpo, modular e documentado.

---

**💡 Dica:** Para uma melhor experiência de desenvolvimento, use o VSCode com as extensões:
- PHP Intelephense
- Laravel Blade Snippets
- Tailwind CSS IntelliSense
- GitLens

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
