<?php

namespace App\Repositories;

use App\DTOs\LayerData;
use App\Models\Layer;
use Illuminate\Database\Eloquent\Collection;

interface LayerRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Layer;

    public function create(LayerData $data): Layer;

    public function update(int $id, LayerData $data): Layer;

    public function delete(int $id): bool;
}
