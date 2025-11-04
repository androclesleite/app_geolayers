<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LayerService;
use Illuminate\Http\JsonResponse;

class LayerController extends Controller
{
    public function __construct(
        private readonly LayerService $layerService
    ) {}

    public function index(): JsonResponse
    {
        $layers = $this->layerService->getAllLayers();

        $features = [];

        foreach ($layers as $layer) {
            $geometryData = json_decode($layer->geometry->toJson(), true);

            if ($geometryData['type'] === 'GeometryCollection') {
                foreach ($geometryData['geometries'] as $index => $geom) {
                    $features[] = [
                        'type' => 'Feature',
                        'id' => $layer->id . '-' . $index,
                        'properties' => [
                            'name' => $layer->name,
                            'created_at' => $layer->created_at?->toISOString(),
                            'layer_id' => $layer->id,
                        ],
                        'geometry' => $geom,
                    ];
                }
            } else {
                $features[] = [
                    'type' => 'Feature',
                    'id' => $layer->id,
                    'properties' => [
                        'name' => $layer->name,
                        'created_at' => $layer->created_at?->toISOString(),
                    ],
                    'geometry' => $geometryData,
                ];
            }
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $layer = $this->layerService->findLayer($id);

        if (!$layer) {
            return response()->json(['error' => 'Camada não encontrada'], 404);
        }

        return response()->json([
            'type' => 'Feature',
            'id' => $layer->id,
            'properties' => [
                'name' => $layer->name,
                'created_at' => $layer->created_at?->toISOString(),
            ],
            'geometry' => json_decode($layer->geometry->toJson(), true),
        ]);
    }
}
