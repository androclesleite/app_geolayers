<?php

namespace App\Repositories;

use App\DTOs\LayerData;
use App\Models\Layer;
use Illuminate\Database\Eloquent\Collection;

class LayerRepository implements LayerRepositoryInterface
{
    public function __construct(
        private readonly Layer $model
    ) {
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Layer
    {
        return $this->model->find($id);
    }

    public function create(LayerData $data): Layer
    {
        return $this->model->create([
            'name' => $data->name,
            'geometry' => $data->geometry,
        ]);
    }

    public function update(int $id, LayerData $data): Layer
    {
        $layer = $this->find($id);

        $layer->update([
            'name' => $data->name,
            'geometry' => $data->geometry,
        ]);

        return $layer->fresh();
    }

    public function delete(int $id): bool
    {
        $layer = $this->find($id);

        return $layer?->delete() ?? false;
    }
}
