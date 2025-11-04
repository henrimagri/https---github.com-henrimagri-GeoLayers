# 🗺️ Como Funciona o Mapa - Visualização de Camadas

## Como as Camadas Aparecem no Mapa?

### 1️⃣ **Tipos de Geometria e Visualização**

Cada tipo de geometria GeoJSON é renderizado de forma diferente no mapa:

#### 📍 **Point (Ponto)**
- **Aparência:** Círculo colorido
- **Tamanho:** 12 pixels
- **Borda:** Branca (2px)
- **Opacidade:** 90%
- **Exemplo:** Localização de cidades, pontos de interesse

#### 📏 **LineString (Linha)**
- **Aparência:** Linha colorida sólida
- **Espessura:** 4 pixels
- **Opacidade:** 100%
- **Estilo:** Pontas arredondadas
- **Exemplo:** Rodovias, rios, rotas

#### 🔲 **Polygon (Polígono)**
- **Aparência:** Área preenchida com borda
- **Preenchimento:** 50% de opacidade
- **Borda:** 3 pixels sólida
- **Exemplo:** Estados, municípios, áreas protegidas

### 2️⃣ **Cores das Camadas**

O sistema atribui cores automaticamente para cada camada:

| Ordem | Cor | RGB | Uso |
|-------|-----|-----|-----|
| 1ª | 🟣 Roxo | (102, 126, 234) | Primeira camada |
| 2ª | 🔴 Vermelho | (220, 53, 69) | Segunda camada |
| 3ª | 🟢 Verde | (40, 167, 69) | Terceira camada |
| 4ª | 🟡 Amarelo | (255, 193, 7) | Quarta camada |
| 5ª | 🔵 Ciano | (23, 162, 184) | Quinta camada |
| 6ª | 🟠 Laranja | (253, 126, 20) | Sexta camada |
| 7ª | 🩷 Rosa | (232, 62, 140) | Sétima camada |

As cores se repetem após a 7ª camada.

### 3️⃣ **Zoom Automático**

Quando você acessa a página do mapa:

1. **Carrega todas as camadas** da API (`/api/layers`)
2. **Renderiza no mapa** cada geometria com sua cor
3. **Faz zoom automático** para enquadrar TODAS as camadas
4. **Duração:** 2 segundos com animação suave

### 4️⃣ **Painel Lateral de Controle**

No canto superior direito aparece o painel com:

```
📁 Camadas Disponíveis
┌────────────────────────┐
│ ☑️ Nome da Camada 🟣   │
│ ☑️ Outra Camada 🔴     │
│ ☐ Camada Oculta 🟢    │
└────────────────────────┘
```

- **Checkbox marcado** = Camada visível
- **Checkbox desmarcado** = Camada oculta
- **Quadrado colorido** = Cor da camada no mapa

### 5️⃣ **Interação no Mapa**

#### **Clique em uma Feature:**
Aparece um popup com:
```
┌─────────────────────┐
│ Nome da Camada      │
├─────────────────────┤
│ Camada: saddsasad   │
│ Criada em: 04/11/25 │
└─────────────────────┘
```

#### **Navegação:**
- **Scroll do mouse:** Zoom in/out
- **Clique e arraste:** Move o mapa
- **Shift + Arraste:** Rotação do mapa
- **Ctrl + Clique:** Zoom na área selecionada

### 6️⃣ **Exemplo Prático**

Sua camada "saddsasad" contém **3 geometrias**:

1. **1 Ponto** em Fortaleza/CE
   - Coordenadas: -38.537°, -3.741°
   - Aparece como: 🔵 Círculo roxo

2. **1 Linha** conectando 4 pontos
   - Coordenadas: várias em Fortaleza
   - Aparece como: ━━━ Linha roxa

3. **1 Polígono** retangular
   - Área em Fortaleza
   - Aparece como: ▭ Retângulo roxo semitransparente

### 7️⃣ **Como Testar Agora**

1. **Inicie o servidor:**
   ```powershell
   php artisan serve
   ```

2. **Acesse o mapa:**
   ```
   http://127.0.0.1:8000
   ```

3. **O que você deve ver:**
   - ✅ Loading de 0.5 segundos
   - ✅ Mapa faz zoom automático para Fortaleza/CE
   - ✅ Painel lateral aparece com "saddsasad"
   - ✅ 3 elementos roxos no mapa (ponto + linha + polígono)

4. **Interaja:**
   - Clique no ponto roxo → Vê popup
   - Clique na linha roxa → Vê popup
   - Clique no polígono → Vê popup
   - Desmarque checkbox → Camada desaparece
   - Marque novamente → Camada reaparece

### 8️⃣ **Troubleshooting**

#### **Não vejo nada no mapa:**

1. **Abra o Console (F12)** e verifique:
   ```javascript
   // Deve aparecer:
   Camadas carregadas: {type: "FeatureCollection", features: [...]}
   Criando camada: saddsasad {type: "GeometryCollection", ...}
   Camada saddsasad criada com 3 geometrias
   ```

2. **Verifique a API:**
   ```powershell
   curl http://127.0.0.1:8000/api/layers
   ```
   Deve retornar JSON com suas camadas

3. **Limpe o cache do navegador:**
   - Ctrl + Shift + Delete
   - Ou Ctrl + F5 (hard refresh)

#### **Camada aparece mas está muito longe:**

- O zoom automático leva até 2 segundos
- Aguarde a animação completar
- Se ainda estiver longe, clique no painel lateral e desmarque/marque a camada

#### **Quero adicionar mais camadas:**

1. Faça login: http://127.0.0.1:8000/login
2. Vá para: http://127.0.0.1:8000/painel/layers
3. Clique em "Nova Camada"
4. Faça upload de um arquivo GeoJSON
5. Volte para http://127.0.0.1:8000

### 9️⃣ **Exemplos de GeoJSON**

#### **Ponto Simples:**
```json
{
  "type": "Feature",
  "properties": { "name": "Meu Local" },
  "geometry": {
    "type": "Point",
    "coordinates": [-46.6333, -23.5505]
  }
}
```

#### **Linha:**
```json
{
  "type": "Feature",
  "properties": { "name": "Rota" },
  "geometry": {
    "type": "LineString",
    "coordinates": [
      [-46.6333, -23.5505],
      [-46.6500, -23.5600]
    ]
  }
}
```

#### **Polígono:**
```json
{
  "type": "Feature",
  "properties": { "name": "Área" },
  "geometry": {
    "type": "Polygon",
    "coordinates": [[
      [-46.6, -23.5],
      [-46.7, -23.5],
      [-46.7, -23.6],
      [-46.6, -23.6],
      [-46.6, -23.5]
    ]]
  }
}
```

### 🔟 **Próximos Passos**

- Teste com os arquivos de exemplo em `examples/`
- Crie suas próprias camadas em https://geojson.io
- Experimente combinar diferentes tipos de geometria
- Ajuste as cores editando `layerColors` em `mapa.blade.php`

---

**🎯 Resumo:**
As camadas aparecem no mapa como **pontos, linhas ou polígonos coloridos** dependendo do tipo de geometria GeoJSON. O mapa **faz zoom automático** para enquadrar todas as camadas e você pode **ligar/desligar** cada camada no painel lateral.
