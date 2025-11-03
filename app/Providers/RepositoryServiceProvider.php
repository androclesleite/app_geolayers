<?php

namespace App\Providers;

use App\Repositories\LayerRepository;
use App\Repositories\LayerRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            LayerRepositoryInterface::class,
            LayerRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
