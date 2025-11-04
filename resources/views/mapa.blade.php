<!DOCTYPE HTML>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Mapa de Camadas Geográficas</title>
    
    <!-- ArcGIS Maps SDK -->
    <link rel="stylesheet" href="https://js.arcgis.com/4.31/esri/themes/light/main.css">
    <script src="https://js.arcgis.com/4.31/"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .header-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.5rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: white;
            color: #667eea;
        }

        .btn-primary:hover {
            background: #f7fafc;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .btn-secondary {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.3);
        }

        /* Mapa */
        #viewDiv {
            position: absolute;
            top: 60px;
            left: 0;
            right: 0;
            bottom: 0;
        }

        /* Painel de Camadas */
        .layers-panel {
            position: fixed;
            top: 80px;
            right: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            padding: 1.5rem;
            max-width: 320px;
            max-height: calc(100vh - 120px);
            overflow-y: auto;
            z-index: 99;
            display: none;
        }

        .layers-panel h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .layer-item {
            padding: 0.75rem;
            margin-bottom: 0.75rem;
            background: #f7fafc;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
        }

        .layer-item:hover {
            background: #edf2f7;
            transform: translateX(4px);
        }

        .layer-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #667eea;
        }

        .layer-item label {
            cursor: pointer;
            flex: 1;
            color: #4a5568;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .layer-color {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            border: 2px solid #e2e8f0;
        }

        /* Loading */
        .loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 2rem 3rem;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            text-align: center;
            z-index: 1000;
        }

        .loading.hidden {
            display: none;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e2e8f0;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        .loading p {
            color: #4a5568;
            font-size: 1.1rem;
            font-weight: 500;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #718096;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin: 0 auto 1rem;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>🗺️ {{ config('app.name') }}</h1>
        <div class="header-buttons">
            @auth
                <a href="{{ route('painel.layers.index') }}" class="btn btn-primary">Painel Administrativo</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-secondary">Entrar</a>
            @endauth
        </div>
    </div>

    <!-- Loading -->
    <div id="loading" class="loading">
        <div class="spinner"></div>
        <p>Carregando camadas...</p>
    </div>

    <!-- Painel de Camadas -->
    <div id="layersPanel" class="layers-panel">
        <h3>📁 Camadas Disponíveis</h3>
        <div id="layersList"></div>
    </div>

    <!-- Mapa -->
    <div id="viewDiv"></div>

    <script>
        require([
            "esri/Map",
            "esri/views/MapView",
            "esri/layers/FeatureLayer",
            "esri/Graphic",
            "esri/geometry/Polygon",
            "esri/geometry/Polyline",
            "esri/geometry/Point"
        ], function(Map, MapView, FeatureLayer, Graphic, Polygon, Polyline, Point) {

            const layerColors = [
                [102, 126, 234], // Roxo
                [220, 53, 69],   // Vermelho
                [40, 167, 69],   // Verde
                [255, 193, 7],   // Amarelo
                [23, 162, 184],  // Ciano
                [253, 126, 20],  // Laranja
                [232, 62, 140],  // Rosa
            ];

            const map = new Map({
                basemap: "streets-navigation-vector"
            });

            const view = new MapView({
                container: "viewDiv",
                map: map,
                center: [-47.9292, -15.7801],
                zoom: 4
            });

            const featureLayers = [];

            // Carrega camadas da API
            fetch('/api/layers')
                .then(response => response.json())
                .then(data => {
                    if (!data.features || data.features.length === 0) {
                        showEmptyState();
                        return;
                    }

                    data.features.forEach((feature, index) => {
                        const layerName = feature.properties.name;
                        const color = layerColors[index % layerColors.length];
                        const featureLayer = createFeatureLayer(feature, layerName, color);
                        map.add(featureLayer);
                        featureLayers.push({ layer: featureLayer, name: layerName, color });
                    });

                    document.getElementById('loading').classList.add('hidden');
                    document.getElementById('layersPanel').style.display = 'block';
                    renderLayersPanel();

                    // Aguarda o mapa carregar e faz zoom nas camadas
                    view.when(() => {
                        // Pequeno delay para garantir que as camadas foram adicionadas
                        setTimeout(() => {
                            const allLayers = featureLayers.map(fl => fl.layer);
                            if (allLayers.length > 0) {
                                view.goTo(allLayers, {
                                    duration: 2000,
                                    easing: "ease-in-out"
                                }).catch(err => {
                                    console.warn("Não foi possível fazer zoom automático:", err);
                                });
                            }
                        }, 500);
                    });
                })
                .catch(error => {
                    console.error('Erro ao carregar camadas:', error);
                    document.getElementById('loading').innerHTML = `
                        <p style="color: #e53e3e;">Erro ao carregar camadas</p>
                        <p style="font-size: 0.9rem; color: #718096; margin-top: 0.5rem;">Verifique sua conexão e tente novamente.</p>
                    `;
                });

            function createFeatureLayer(feature, name, color) {
                const geometry = feature.geometry;
                const graphics = [];

                if (geometry.type === 'GeometryCollection') {
                    geometry.geometries.forEach(geom => {
                        const graphic = createGraphic(geom, feature.properties, color);
                        if (Array.isArray(graphic)) {
                            graphics.push(...graphic);
                        } else if (graphic) {
                            graphics.push(graphic);
                        }
                    });
                } else {
                    const graphic = createGraphic(geometry, feature.properties, color);
                    if (Array.isArray(graphic)) {
                        graphics.push(...graphic);
                    } else if (graphic) {
                        graphics.push(graphic);
                    }
                }

                return new FeatureLayer({
                    title: name,
                    source: graphics,
                    objectIdField: "ObjectID",
                    fields: [
                        { name: "ObjectID", type: "oid" },
                        { name: "name", type: "string" }
                    ],
                    renderer: {
                        type: "simple",
                        symbol: createSymbol(geometry.type, color)
                    },
                    popupTemplate: {
                        title: name,
                        content: `<b>Camada:</b> ${name}<br><b>Criada em:</b> ${feature.properties.created_at || 'N/A'}`
                    }
                });
            }

            function createGraphic(geojsonGeometry, properties, color) {
                let esriGeometry;
                let objectId = Math.floor(Math.random() * 1000000);

                switch (geojsonGeometry.type) {
                    case 'Point':
                        esriGeometry = new Point({
                            longitude: geojsonGeometry.coordinates[0],
                            latitude: geojsonGeometry.coordinates[1],
                            spatialReference: { wkid: 4326 }
                        });
                        break;

                    case 'LineString':
                        esriGeometry = new Polyline({
                            paths: [geojsonGeometry.coordinates],
                            spatialReference: { wkid: 4326 }
                        });
                        break;

                    case 'Polygon':
                        esriGeometry = new Polygon({
                            rings: geojsonGeometry.coordinates,
                            spatialReference: { wkid: 4326 }
                        });
                        break;

                    case 'MultiPoint':
                        return geojsonGeometry.coordinates.map((coord, idx) => 
                            new Graphic({
                                geometry: new Point({ 
                                    longitude: coord[0], 
                                    latitude: coord[1],
                                    spatialReference: { wkid: 4326 }
                                }),
                                attributes: { ObjectID: objectId + idx, ...properties },
                                symbol: createSymbol('Point', color)
                            })
                        );

                    case 'MultiLineString':
                        esriGeometry = new Polyline({
                            paths: geojsonGeometry.coordinates,
                            spatialReference: { wkid: 4326 }
                        });
                        break;

                    case 'MultiPolygon':
                        esriGeometry = new Polygon({
                            rings: geojsonGeometry.coordinates.flat(),
                            spatialReference: { wkid: 4326 }
                        });
                        break;

                    default:
                        console.warn('Tipo de geometria não suportado:', geojsonGeometry.type);
                        return null;
                }

                return new Graphic({
                    geometry: esriGeometry,
                    attributes: { ObjectID: objectId, ...properties },
                    symbol: createSymbol(geojsonGeometry.type, color)
                });
            }

            function createSymbol(geometryType, color) {
                const [r, g, b] = color;

                if (geometryType.includes('Point')) {
                    return {
                        type: "simple-marker",
                        color: [r, g, b, 0.9],
                        outline: { 
                            color: [255, 255, 255], 
                            width: 2 
                        },
                        size: 12,
                        style: "circle"
                    };
                } else if (geometryType.includes('Line')) {
                    return {
                        type: "simple-line",
                        color: [r, g, b, 1],
                        width: 4,
                        style: "solid",
                        cap: "round",
                        join: "round"
                    };
                } else {
                    return {
                        type: "simple-fill",
                        color: [r, g, b, 0.5],
                        outline: { 
                            color: [r, g, b], 
                            width: 3,
                            style: "solid"
                        },
                        style: "solid"
                    };
                }
            }

            function renderLayersPanel() {
                const layersList = document.getElementById('layersList');
                layersList.innerHTML = '';

                featureLayers.forEach((fl, index) => {
                    const div = document.createElement('div');
                    div.className = 'layer-item';

                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.id = `layer-${index}`;
                    checkbox.checked = true;
                    checkbox.addEventListener('change', () => {
                        fl.layer.visible = checkbox.checked;
                    });

                    const label = document.createElement('label');
                    label.htmlFor = `layer-${index}`;
                    label.textContent = fl.name;

                    const colorBox = document.createElement('div');
                    colorBox.className = 'layer-color';
                    colorBox.style.backgroundColor = `rgb(${fl.color.join(',')})`;

                    div.appendChild(checkbox);
                    div.appendChild(label);
                    div.appendChild(colorBox);

                    layersList.appendChild(div);
                });
            }

            function showEmptyState() {
                document.getElementById('loading').innerHTML = `
                    <div class="empty-state">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>Nenhuma camada cadastrada ainda.</p>
                        @auth
                        <a href="{{ route('painel.layers.create') }}" class="btn btn-primary" style="display: inline-block; margin-top: 1rem;">Criar Primeira Camada</a>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-primary" style="display: inline-block; margin-top: 1rem;">Fazer Login</a>
                        @endauth
                    </div>
                `;
            }
        });
    </script>
</body>
</html>
