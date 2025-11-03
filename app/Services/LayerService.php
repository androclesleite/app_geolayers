<?php

namespace App\Services;

use App\Actions\ProcessGeoJsonFileAction;
use App\DTOs\LayerData;
use App\Models\Layer;
use App\Repositories\LayerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

class LayerService
{
    public function __construct(
        private readonly LayerRepositoryInterface $repository,
        private readonly ProcessGeoJsonFileAction $processGeoJsonAction
    ) {
    }

    public function getAllLayers(): Collection
    {
        return $this->repository->all();
    }

    public function findLayer(int $id): ?Layer
    {
        return $this->repository->find($id);
    }

    public function createLayer(string $name, UploadedFile $geoJsonFile): Layer
    {
        $geometry = $this->processGeoJsonAction->execute($geoJsonFile);

        $layerData = new LayerData(
            name: $name,
            geometry: $geometry
        );

        return $this->repository->create($layerData);
    }

    public function updateLayer(int $id, string $name, ?UploadedFile $geoJsonFile = null): Layer
    {
        $layer = $this->repository->find($id);

        $geometry = $geoJsonFile
            ? $this->processGeoJsonAction->execute($geoJsonFile)
            : $layer->geometry;

        $layerData = new LayerData(
            name: $name,
            geometry: $geometry,
            id: $id
        );

        return $this->repository->update($id, $layerData);
    }

    public function deleteLayer(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
