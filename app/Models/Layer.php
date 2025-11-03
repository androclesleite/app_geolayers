<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Geometry;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class Layer extends Model
{
    use HasFactory, HasSpatial;

    protected $fillable = [
        'name',
        'geometry',
    ];

    protected $casts = [
        'geometry' => Geometry::class,
    ];
}
