<?php

namespace App\DTOs;

use MatanYadaev\EloquentSpatial\Objects\Geometry;

readonly class LayerData
{
    public function __construct(
        public string $name,
        public Geometry|string $geometry,
        public ?int $id = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            geometry: $data['geometry'],
            id: $data['id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'geometry' => $this->geometry,
        ];
    }
}
