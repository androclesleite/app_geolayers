<?php

namespace App\Filament\Resources\Layers\Pages;

use App\Filament\Resources\Layers\LayerResource;
use App\Services\LayerService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\UploadedFile;

class EditLayer extends EditRecord
{
    protected static string $resource = LayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $layerService = app(LayerService::class);

        $file = isset($data['geojson_file']) && $data['geojson_file'] instanceof UploadedFile
            ? $data['geojson_file']
            : null;

        $layer = $layerService->updateLayer(
            $this->record->id,
            $data['name'],
            $file
        );

        return [
            'name' => $layer->name,
            'geometry' => $layer->geometry,
        ];
    }
}
