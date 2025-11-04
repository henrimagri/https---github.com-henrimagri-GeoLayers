# 📋 Exemplos de GeoJSON

Esta pasta contém arquivos GeoJSON de exemplo para testar a aplicação.

## 📂 Arquivos Disponíveis

### 1. `cidades-brasileiras.geojson`
**Tipo:** Point (Pontos)
**Descrição:** Principais cidades brasileiras com população
**Features:** 5 cidades

Use este arquivo para testar upload de pontos geográficos.

### 2. `rodovias.geojson`
**Tipo:** LineString (Linhas)
**Descrição:** Rodovias brasileiras
**Features:** 2 rodovias

Use este arquivo para testar upload de linhas (rotas, estradas).

### 3. `areas-interesse.geojson`
**Tipo:** Polygon (Polígonos)
**Descrição:** Áreas de interesse em São Paulo
**Features:** 2 áreas (Parque Ibirapuera e Centro Histórico)

Use este arquivo para testar upload de polígonos (áreas, regiões).

## 🧪 Como Usar

1. Faça login no painel: http://localhost:8000/login
2. Acesse: http://localhost:8000/painel/layers
3. Clique em "Nova Camada"
4. Preencha o nome (ex: "Cidades do Brasil")
5. Faça upload de um dos arquivos `.geojson`
6. Clique em "Criar Camada"
7. Acesse a página inicial para ver no mapa: http://localhost:8000

## 🌐 Criar seus próprios GeoJSON

### Ferramentas Online
- **geojson.io** - https://geojson.io
  - Editor visual e interativo
  - Desenhe no mapa e exporte
  
### Software Desktop
- **QGIS** - https://qgis.org
  - GIS profissional e gratuito
  - Exporte para GeoJSON

### Programaticamente
```python
# Python com GeoPandas
import geopandas as gpd

# Criar GeoDataFrame
gdf = gpd.GeoDataFrame(
    {'name': ['Ponto A', 'Ponto B']},
    geometry=gpd.points_from_xy([-46.6, -43.1], [-23.5, -22.9])
)

# Salvar como GeoJSON
gdf.to_file('meu-arquivo.geojson', driver='GeoJSON')
```

```javascript
// JavaScript
const geojson = {
  type: "FeatureCollection",
  features: [
    {
      type: "Feature",
      properties: { name: "Meu Ponto" },
      geometry: {
        type: "Point",
        coordinates: [-46.6333, -23.5505]
      }
    }
  ]
};

// Converter para JSON
const json = JSON.stringify(geojson, null, 2);
```

## 📐 Formato GeoJSON

### Estrutura Básica

```json
{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "properties": {
        "propriedade1": "valor1",
        "propriedade2": "valor2"
      },
      "geometry": {
        "type": "TipoDeGeometria",
        "coordinates": [...]
      }
    }
  ]
}
```

### Tipos de Geometria

#### Point (Ponto)
```json
{
  "type": "Point",
  "coordinates": [longitude, latitude]
}
```

#### LineString (Linha)
```json
{
  "type": "LineString",
  "coordinates": [
    [longitude1, latitude1],
    [longitude2, latitude2]
  ]
}
```

#### Polygon (Polígono)
```json
{
  "type": "Polygon",
  "coordinates": [[
    [longitude1, latitude1],
    [longitude2, latitude2],
    [longitude3, latitude3],
    [longitude1, latitude1]
  ]]
}
```

## ⚠️ Observações

- As coordenadas devem estar em formato `[longitude, latitude]`
- O sistema de coordenadas é **WGS84 (SRID 4326)**
- Para polígonos, o primeiro e último ponto devem ser iguais (fechar o polígono)
- Longitude: -180 a 180 (oeste negativo, leste positivo)
- Latitude: -90 a 90 (sul negativo, norte positivo)

## 🗺️ Coordenadas do Brasil

Para referência:

| Cidade | Longitude | Latitude |
|--------|-----------|----------|
| São Paulo | -46.6333 | -23.5505 |
| Rio de Janeiro | -43.1729 | -22.9068 |
| Brasília | -47.9292 | -15.7801 |
| Salvador | -38.5108 | -12.9714 |
| Fortaleza | -38.5434 | -3.7172 |
| Belo Horizonte | -43.9378 | -19.9208 |
| Manaus | -60.0217 | -3.1190 |
| Curitiba | -49.2710 | -25.4290 |
| Recife | -34.8770 | -8.0476 |
| Porto Alegre | -51.2302 | -30.0346 |

## 📚 Recursos Adicionais

- [GeoJSON Specification](https://geojson.org/)
- [GeoJSON Lint (Validador)](https://geojsonlint.com/)
- [Awesome GeoJSON](https://github.com/tmcw/awesome-geojson)
