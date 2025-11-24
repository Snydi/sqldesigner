<?php

namespace App\Providers;

use App\Repositories\DiagramRepository;
use App\Repositories\DiagramRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DiagramRepositoryInterface::class, DiagramRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
