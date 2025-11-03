<?php

namespace App\Actions;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use MatanYadaev\EloquentSpatial\Objects\Geometry;

class ProcessGeoJsonFileAction
{
    public function execute(UploadedFile $file): Geometry
    {
        if ($file->getClientOriginalExtension() !== 'geojson' && $file->getMimeType() !== 'application/json') {
            throw new InvalidArgumentException('O arquivo deve ser um GeoJSON válido');
        }

        $geoJsonContent = file_get_contents($file->getRealPath());

        $decoded = json_decode($geoJsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('GeoJSON inválido: erro ao decodificar JSON');
        }

        if (!isset($decoded['type'])) {
            throw new InvalidArgumentException('GeoJSON inválido: propriedade "type" não encontrada');
        }

        try {
            return Geometry::fromJson($geoJsonContent);
        } catch (\Exception $e) {
            throw new InvalidArgumentException('Erro ao processar GeoJSON: ' . $e->getMessage());
        }
    }
}
