# ⚡ Início Rápido - GeoLayers Manager

Comece em **5 minutos**! 🚀

## 📦 Opção 1: Com Docker (Mais Fácil)

```bash
# 1. Subir PostgreSQL + PostGIS
docker-compose up -d

# 2. Instalar dependências
composer install
npm install

# 3. Instalar autenticação
php artisan breeze:install blade

# 4. Configurar banco
php artisan migrate
php artisan db:seed --class=AdminUserSeeder

# 5. Compilar assets
npm run build

# 6. Iniciar servidor
php artisan serve
```

**Acesse:** http://localhost:8000

**Login:**
- Email: `admin@geolayers.com`
- Senha: `password`

## 🛠️ Opção 2: Sem Docker

### Pré-requisitos
- PostgreSQL instalado
- Extensão PostGIS instalada

```bash
# 1. Criar banco de dados
psql -U postgres -c "CREATE DATABASE geolayers;"
psql -U postgres -d geolayers -c "CREATE EXTENSION postgis;"

# 2. Instalar dependências
composer install
npm install

# 3. Instalar autenticação
php artisan breeze:install blade

# 4. Configurar banco
php artisan migrate
php artisan db:seed --class=AdminUserSeeder

# 5. Compilar assets
npm run build

# 6. Iniciar servidor
php artisan serve
```

**Acesse:** http://localhost:8000

## 🧪 Testar com Dados de Exemplo

```bash
# O projeto já inclui 3 arquivos GeoJSON de exemplo:
ls examples/

# 1. Faça login no painel
# 2. Acesse: http://localhost:8000/painel/layers
# 3. Clique em "Nova Camada"
# 4. Upload: examples/cidades-brasileiras.geojson
# 5. Nome: "Cidades do Brasil"
# 6. Clique em "Criar Camada"
# 7. Acesse http://localhost:8000 para ver no mapa!
```

## 🗺️ Primeiros Passos

### 1️⃣ Criar sua primeira camada

1. **Login:** http://localhost:8000/login
   - Email: `admin@geolayers.com`
   - Senha: `password`

2. **Painel:** http://localhost:8000/painel/layers
   - Clique em "Nova Camada"

3. **Upload GeoJSON:**
   - Use: `examples/cidades-brasileiras.geojson`
   - Nome: "Principais Cidades"
   - Salvar

4. **Ver no Mapa:** http://localhost:8000
   - Sua camada aparecerá automaticamente!

### 2️⃣ Criar GeoJSON próprio

**Método 1: Online (geojson.io)**
1. Acesse: https://geojson.io
2. Desenhe pontos/linhas/polígonos
3. Copie o JSON
4. Salve como `.geojson`
5. Faça upload!

**Método 2: Manualmente**
```json
{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "properties": { "name": "Meu Local" },
      "geometry": {
        "type": "Point",
        "coordinates": [-46.6333, -23.5505]
      }
    }
  ]
}
```

### 3️⃣ Usar a API

```bash
# Listar todas as camadas
curl http://localhost:8000/api/layers

# Ver camada específica
curl http://localhost:8000/api/layers/1

# Buscar por ponto (São Paulo)
curl "http://localhost:8000/api/layers/contains?longitude=-46.6333&latitude=-23.5505"

# Buscar por distância (1km de raio)
curl "http://localhost:8000/api/layers/within?longitude=-46.6333&latitude=-23.5505&distance=1000"
```

## 🎨 Interface

### Páginas Principais

| URL | O que faz |
|-----|-----------|
| `/` | Mapa interativo com todas as camadas |
| `/login` | Login no sistema |
| `/painel/layers` | Gerenciar camadas (CRUD) |
| `/painel/layers/create` | Criar nova camada |
| `/api/layers` | API REST (GeoJSON) |

## 🔧 Comandos Essenciais

```bash
# Servidor
php artisan serve

# Assets (desenvolvimento)
npm run dev

# Limpar cache
php artisan optimize:clear

# Ver rotas
php artisan route:list

# Console interativo
php artisan tinker

# Ver logs
tail -f storage/logs/laravel.log
```

## 📱 Recursos

| Arquivo | O que tem |
|---------|-----------|
| `README.md` | Documentação completa |
| `SETUP.md` | Instalação detalhada |
| `COMMANDS.md` | Todos os comandos |
| `CHECKLIST.md` | Verificar se está tudo OK |
| `examples/` | GeoJSON de exemplo |

## ⚠️ Problemas Comuns

### Erro: "could not find driver"
```bash
# Instale extensão PostgreSQL do PHP
# Ubuntu/Debian:
sudo apt-get install php8.2-pgsql

# Windows:
# Descomente no php.ini: extension=pdo_pgsql
```

### Erro: "PostGIS extension not found"
```sql
-- Execute no PostgreSQL:
CREATE EXTENSION postgis;
```

### Erro de permissão (storage)
```bash
# Linux/Mac:
chmod -R 775 storage bootstrap/cache

# Windows PowerShell:
icacls storage /grant Everyone:F /T
```

### Mapa não carrega
1. Verifique se servidor está rodando
2. Abra console do navegador (F12)
3. Teste a API: http://localhost:8000/api/layers

## 🚀 Próximo Nível

Depois que estiver funcionando:

1. **Leia a documentação completa:** `README.md`
2. **Explore os arquivos de exemplo:** `examples/`
3. **Teste a API REST:** Use Postman ou curl
4. **Customize:** Modifique cores, estilos, adicione features
5. **Deploy:** Veja `README.md` seção de produção

## 💡 Dicas

- Use `geojson.io` para criar/validar GeoJSON
- Coordenadas são `[longitude, latitude]` (não latitude, longitude!)
- SRID 4326 = WGS84 (padrão GPS)
- Polígonos devem fechar (primeiro ponto = último ponto)
- Tamanho máximo de upload: 10MB

## 🆘 Precisa de Ajuda?

1. Consulte `CHECKLIST.md`
2. Veja logs: `storage/logs/laravel.log`
3. Console do navegador (F12)
4. Verifique `COMMANDS.md`

## ✅ Tudo Funcionando?

Se você conseguiu:
- ✅ Fazer login
- ✅ Criar uma camada
- ✅ Ver no mapa
- ✅ API respondendo

**Parabéns! 🎉** Está tudo OK!

Agora explore o código e customize!

---

**⏱️ Tempo total:** ~5 minutos

**📊 Complexidade:** Baixa

**🎯 Objetivo:** Você rodando em minutos!
