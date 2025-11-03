<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GeoLayers - Visualizador de Camadas</title>

    <link rel="stylesheet" href="https://js.arcgis.com/4.31/esri/themes/light/main.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }

        #viewDiv {
            width: 100%;
            height: 100vh;
        }

        .header {
            position: absolute;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.95);
            padding: 15px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            z-index: 10;
            text-align: center;
        }

        .header h1 {
            font-size: 22px;
            color: #333;
            margin: 0;
        }

        .admin-link {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #0078d4;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            z-index: 10;
            transition: background 0.3s;
        }

        .admin-link:hover {
            background: #005a9e;
        }

        .layers-panel {
            position: absolute;
            top: 80px;
            left: 15px;
            width: 280px;
            max-height: calc(100vh - 100px);
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            z-index: 10;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .panel-header {
            padding: 15px;
            background: #f5f5f5;
            border-bottom: 1px solid #ddd;
            font-weight: 600;
            color: #333;
        }

        .layers-list {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
        }

        .layer-item {
            padding: 12px;
            margin-bottom: 8px;
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .layer-item:hover {
            background: #e3f2fd;
            border-color: #0078d4;
            transform: translateX(3px);
        }

        .layer-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }

        .layer-type {
            font-size: 12px;
            color: #666;
            background: #e0e0e0;
            padding: 2px 8px;
            border-radius: 3px;
            display: inline-block;
        }

        .empty-state {
            padding: 20px;
            text-align: center;
            color: #999;
        }

        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 100;
            font-size: 16px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>GeoLayers - Visualizador de Camadas Geográficas</h1>
    </div>

    <a href="/painel" class="admin-link">Painel Admin</a>

    <div class="layers-panel">
        <div class="panel-header">Camadas Cadastradas</div>
        <div class="layers-list" id="layersList">
            <div class="empty-state">Carregando...</div>
        </div>
    </div>

    <div id="viewDiv"></div>
    <div id="loading" class="loading" style="display:none;">Carregando mapa...</div>

    <script src="https://js.arcgis.com/4.31/"></script>
    <script>
        require([
            "esri/Map",
            "esri/views/MapView",
            "esri/layers/GeoJSONLayer",
            "esri/widgets/Legend",
            "esri/widgets/Expand"
        ], function(Map, MapView, GeoJSONLayer, Legend, Expand) {

            const esriMap = new Map({
                basemap: "streets-navigation-vector"
            });

            const view = new MapView({
                container: "viewDiv",
                map: esriMap,
                center: [-47.8825, -15.7942],
                zoom: 4
            });

            let geojsonLayer = null;

            view.when(() => {
                fetch('/api/layers')
                    .then(response => response.json())
                    .then(data => {
                        const layersList = document.getElementById('layersList');

                        if (data.features && data.features.length > 0) {
                            const blob = new Blob([JSON.stringify(data)], {
                                type: "application/json"
                            });

                            const url = URL.createObjectURL(blob);

                            geojsonLayer = new GeoJSONLayer({
                                url: url,
                                title: "Camadas",
                                copyright: "GeoLayers",
                                popupTemplate: {
                                    title: "{name}",
                                    content: "<b>ID:</b> {layer_id}<br><b>Criado em:</b> {created_at}"
                                }
                            });

                            esriMap.add(geojsonLayer);

                            geojsonLayer.when(
                                () => {
                                    populateLayersList(data.features);

                                    if (geojsonLayer.fullExtent) {
                                        view.goTo(geojsonLayer.fullExtent);
                                    }
                                },
                                (error) => {
                                    console.error('Erro ao carregar layer:', error);
                                    layersList.innerHTML = '<div class="empty-state">Erro ao carregar camadas</div>';
                                }
                            );

                        } else {
                            layersList.innerHTML = '<div class="empty-state">Nenhuma camada cadastrada. <br><a href="/painel">Ir para o Painel</a></div>';
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao carregar camadas:', error);
                        document.getElementById('layersList').innerHTML = '<div class="empty-state">Erro ao carregar</div>';
                    });
            });

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function populateLayersList(features) {
                const layersList = document.getElementById('layersList');
                const layersObj = {};

                features.forEach(feature => {
                    const layerId = feature.properties.layer_id || feature.id;
                    const layerName = feature.properties.name;

                    if (!layersObj[layerName]) {
                        layersObj[layerName] = {
                            id: layerId,
                            name: layerName,
                            features: []
                        };
                    }

                    layersObj[layerName].features.push(feature);
                });

                layersList.innerHTML = '';

                Object.values(layersObj).forEach((layer) => {
                    const layerDiv = document.createElement('div');
                    layerDiv.className = 'layer-item';
                    layerDiv.innerHTML = `
                        <div class="layer-name">${escapeHtml(layer.name)}</div>
                        <span class="layer-type">${layer.features.length} geometria(s)</span>
                    `;

                    layerDiv.addEventListener('click', () => {
                        zoomToLayer(layer.features);
                    });

                    layersList.appendChild(layerDiv);
                });
            }

            function zoomToLayer(features) {
                if (!view || !features || features.length === 0) return;

                let minLon = Infinity, maxLon = -Infinity;
                let minLat = Infinity, maxLat = -Infinity;

                features.forEach(feature => {
                    const geom = feature.geometry;

                    function processCoordsArray(coords) {
                        if (typeof coords[0] === 'number') {
                            minLon = Math.min(minLon, coords[0]);
                            maxLon = Math.max(maxLon, coords[0]);
                            minLat = Math.min(minLat, coords[1]);
                            maxLat = Math.max(maxLat, coords[1]);
                        } else {
                            coords.forEach(processCoordsArray);
                        }
                    }

                    if (geom.coordinates) {
                        processCoordsArray(geom.coordinates);
                    }
                });

                const centerLon = (minLon + maxLon) / 2;
                const centerLat = (minLat + maxLat) / 2;

                view.goTo({
                    center: [centerLon, centerLat],
                    zoom: 14
                });
            }
        });
    </script>
</body>
</html>
