# 🚀 Comandos Úteis - GeoLayers Manager

## 📦 Instalação Inicial

```bash
# 1. Instalar dependências PHP
composer install

# 2. Configurar .env (já está configurado)
# Edite se necessário: DB_PASSWORD, DB_USERNAME, etc.

# 3. Instalar Laravel Breeze
php artisan breeze:install blade

# 4. Executar migrations
php artisan migrate

# 5. Criar usuário admin
php artisan db:seed --class=AdminUserSeeder

# 6. Instalar dependências NPM
npm install

# 7. Compilar assets
npm run build
```

## 🐳 Docker

```bash
# Subir PostgreSQL + PostGIS
docker-compose up -d

# Ver logs
docker-compose logs -f

# Parar containers
docker-compose down

# Parar e remover volumes (apaga dados!)
docker-compose down -v

# Acessar PostgreSQL via psql
docker exec -it geolayers-db psql -U postgres -d geolayers
```

## 🗄️ Banco de Dados

```bash
# Executar migrations
php artisan migrate

# Resetar banco (CUIDADO: apaga tudo!)
php artisan migrate:fresh

# Resetar e popular com dados de teste
php artisan migrate:fresh --seed

# Criar nova migration
php artisan make:migration create_nome_da_tabela

# Ver status das migrations
php artisan migrate:status
```

## 🎨 Assets (CSS/JS)

```bash
# Compilar para produção (minificado)
npm run build

# Modo desenvolvimento (hot reload)
npm run dev

# Limpar cache do Vite
rm -rf node_modules/.vite
```

## 🔧 Laravel Artisan

```bash
# Iniciar servidor de desenvolvimento
php artisan serve

# Iniciar servidor em porta específica
php artisan serve --port=8080

# Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Ver rotas cadastradas
php artisan route:list

# Criar controller
php artisan make:controller NomeController

# Criar model
php artisan make:model NomeModel

# Criar seeder
php artisan make:seeder NomeSeeder

# Executar seeder específico
php artisan db:seed --class=NomeSeeder

# Criar middleware
php artisan make:middleware NomeMiddleware
```

## 🧪 Testes

```bash
# Executar todos os testes
php artisan test

# Executar testes específicos
php artisan test --filter=NomeDoTeste

# Executar com coverage
php artisan test --coverage
```

## 📊 PostgreSQL/PostGIS

```bash
# Conectar ao PostgreSQL
psql -U postgres -d geolayers

# Criar banco de dados
psql -U postgres -c "CREATE DATABASE geolayers;"

# Habilitar PostGIS
psql -U postgres -d geolayers -c "CREATE EXTENSION postgis;"

# Verificar versão do PostGIS
psql -U postgres -d geolayers -c "SELECT PostGIS_Version();"

# Listar tabelas
psql -U postgres -d geolayers -c "\dt"

# Ver estrutura da tabela layers
psql -U postgres -d geolayers -c "\d layers"

# Contar layers cadastradas
psql -U postgres -d geolayers -c "SELECT COUNT(*) FROM layers;"

# Ver todas as layers
psql -U postgres -d geolayers -c "SELECT id, name, created_at FROM layers;"
```

## 🛠️ Manutenção

```bash
# Otimizar autoloader
composer dump-autoload -o

# Limpar arquivos temporários
php artisan optimize:clear

# Gerar key da aplicação (apenas primeira vez)
php artisan key:generate

# Ver informações do sistema
php artisan about

# Verificar permissões (Linux/Mac)
chmod -R 775 storage bootstrap/cache

# Verificar permissões (Windows PowerShell)
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T
```

## 📝 Git

```bash
# Adicionar mudanças
git add .

# Commit
git commit -m "Descrição das mudanças"

# Push
git push origin main

# Ver status
git status

# Ver histórico
git log --oneline

# Criar nova branch
git checkout -b feature/nome-da-feature
```

## 🔍 Debug

```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Ver logs do PostgreSQL (Docker)
docker logs -f geolayers-db

# Executar tinker (console interativo)
php artisan tinker

# Exemplos no tinker:
App\Models\Layer::count()
App\Models\Layer::all()
App\Models\User::first()
```

## 📤 Deploy (Produção)

```bash
# 1. Otimizar autoloader
composer install --optimize-autoloader --no-dev

# 2. Cache de configuração
php artisan config:cache

# 3. Cache de rotas
php artisan route:cache

# 4. Cache de views
php artisan view:cache

# 5. Compilar assets
npm run build

# 6. Ajustar permissões
chmod -R 755 storage bootstrap/cache

# 7. Migrar banco (produção)
php artisan migrate --force
```

## 🧹 Limpeza Total (Reset Completo)

```bash
# ATENÇÃO: Isso apaga TUDO!

# 1. Remover vendors
rm -rf vendor node_modules

# 2. Remover compiled
rm -rf bootstrap/cache/*.php

# 3. Resetar banco
php artisan migrate:fresh

# 4. Reinstalar tudo
composer install
npm install
npm run build
php artisan migrate
php artisan db:seed --class=AdminUserSeeder
```

## 💡 Dicas

```bash
# Ver versão do PHP
php -v

# Ver versão do Composer
composer --version

# Ver versão do Node
node --version

# Ver versão do NPM
npm --version

# Ver versão do PostgreSQL
psql --version

# Ver informações do Laravel
php artisan --version
php artisan about
```
