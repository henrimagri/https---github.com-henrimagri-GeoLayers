# 🚀 Configuração do Ambiente - GeoLayers Manager

## 📋 Pré-requisitos

### 1. PostgreSQL + PostGIS

Você precisa ter PostgreSQL com a extensão PostGIS instalada.

#### Windows (via PostgreSQL Stack Builder):
1. Baixe PostgreSQL: https://www.postgresql.org/download/windows/
2. Durante a instalação, marque "Stack Builder"
3. No Stack Builder, instale PostGIS

#### Docker (Recomendado):
```bash
docker run --name geolayers-db \
  -e POSTGRES_DB=geolayers \
  -e POSTGRES_USER=postgres \
  -e POSTGRES_PASSWORD=postgres \
  -p 5432:5432 \
  -d postgis/postgis:16-3.4
```

### 2. PHP 8.2+ com extensões necessárias
- pdo_pgsql
- pgsql
- mbstring
- fileinfo

### 3. Composer
Download: https://getcomposer.org/download/

### 4. Node.js e NPM
Download: https://nodejs.org/

---

## 🔧 Instalação Passo a Passo

### 1️⃣ Instalar dependências do Composer
```bash
composer install
```

Isso instalará:
- Laravel Framework
- Laravel Breeze (autenticação)
- jaguarjack/laravel-postgis (suporte a dados espaciais)

### 2️⃣ Configurar banco de dados

Edite o arquivo `.env` se necessário (já configurado para PostgreSQL):
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=geolayers
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

### 3️⃣ Criar banco de dados e habilitar PostGIS

Conecte ao PostgreSQL e execute:
```sql
CREATE DATABASE geolayers;
\c geolayers
CREATE EXTENSION postgis;
```

Ou via linha de comando:
```bash
psql -U postgres -c "CREATE DATABASE geolayers;"
psql -U postgres -d geolayers -c "CREATE EXTENSION postgis;"
```

### 4️⃣ Instalar Laravel Breeze (autenticação)
```bash
php artisan breeze:install blade
```

Escolha:
- Blade (stack padrão)
- Dark mode: opcional
- Pest: opcional

### 5️⃣ Executar migrations
```bash
php artisan migrate
```

### 6️⃣ Instalar dependências NPM e compilar assets
```bash
npm install
npm run build
```

Para desenvolvimento com hot reload:
```bash
npm run dev
```

### 7️⃣ Criar usuário administrador

Execute o seeder para criar um usuário de teste:
```bash
php artisan db:seed --class=AdminUserSeeder
```

**Credenciais padrão:**
- Email: admin@geolayers.com
- Senha: password

### 8️⃣ Iniciar servidor
```bash
php artisan serve
```

---

## 🌐 Acessar a aplicação

- **Página Inicial (Mapa):** http://localhost:8000
- **Painel Administrativo:** http://localhost:8000/painel
- **Login:** http://localhost:8000/login

---

## 📦 Estrutura do Projeto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   └── LayerController.php      # API REST para layers
│   │   └── PainelController.php         # CRUD do painel admin
│   ├── Requests/
│   │   ├── StoreLayerRequest.php        # Validação de criação
│   │   └── UpdateLayerRequest.php       # Validação de atualização
│   └── Resources/
│       └── LayerResource.php            # Formatação de resposta API
├── Models/
│   └── Layer.php                        # Model com suporte PostGIS
├── Repositories/
│   └── LayerRepository.php              # Camada de acesso a dados
└── Services/
    └── LayerService.php                 # Regras de negócio

database/
├── migrations/
│   └── xxxx_create_layers_table.php     # Tabela com geometry
└── seeders/
    └── AdminUserSeeder.php              # Usuário admin

resources/
└── views/
    ├── painel/
    │   ├── index.blade.php              # Lista de layers
    │   ├── create.blade.php             # Formulário de criação
    │   └── edit.blade.php               # Formulário de edição
    └── welcome.blade.php                # Mapa principal (ArcGIS)
```

---

## 🗺️ Usando a Aplicação

### Upload de GeoJSON

1. Faça login no painel administrativo
2. Acesse "Camadas" ou `/painel/layers`
3. Clique em "Nova Camada"
4. Preencha o nome e faça upload do arquivo .geojson
5. Clique em "Salvar"

### Visualizar no Mapa

1. Acesse a página inicial `/`
2. Todas as camadas cadastradas serão exibidas automaticamente
3. Clique nas geometrias para ver informações
4. Use o painel lateral para ligar/desligar camadas

---

## 🧪 Testes

Para executar os testes:
```bash
php artisan test
```

---

## 🐛 Troubleshooting

### Erro: "could not find driver"
Instale a extensão `pdo_pgsql` do PHP.

### Erro: "PostGIS extension not found"
Execute no banco:
```sql
CREATE EXTENSION postgis;
```

### Erro de permissão no storage
```bash
chmod -R 775 storage bootstrap/cache
```

Windows PowerShell:
```powershell
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T
```

---

## 📚 Documentação Adicional

- [Laravel Documentation](https://laravel.com/docs)
- [PostGIS Documentation](https://postgis.net/documentation/)
- [ArcGIS Maps SDK for JavaScript](https://developers.arcgis.com/javascript/latest/)
