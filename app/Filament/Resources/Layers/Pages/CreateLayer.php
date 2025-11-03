<?php

namespace App\Filament\Resources\Layers\Pages;

use App\Filament\Resources\Layers\LayerResource;
use App\Services\LayerService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\UploadedFile;

class CreateLayer extends CreateRecord
{
    protected static string $resource = LayerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $layerService = app(LayerService::class);

        if (isset($data['geojson_file'])) {
            $file = $data['geojson_file'];

            if ($file instanceof UploadedFile) {
                $layer = $layerService->createLayer($data['name'], $file);

                return [
                    'id' => $layer->id,
                    'name' => $layer->name,
                    'geometry' => $layer->geometry,
                ];
            }
        }

        throw new \InvalidArgumentException('Arquivo GeoJSON é obrigatório');
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return static::getModel()::find($data['id']);
    }
}
