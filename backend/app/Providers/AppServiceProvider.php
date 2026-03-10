<?php

namespace App\Providers;

use App\Models\Diagram;
use App\Policies\DiagramPolicy;
use App\Repositories\DiagramRepository;
use App\Repositories\DiagramRepositoryInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DiagramRepositoryInterface::class, DiagramRepository::class);
    }

    public function boot(): void
    {
        Gate::policy(Diagram::class, DiagramPolicy::class);
    }
}
