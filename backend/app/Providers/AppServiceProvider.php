<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Diagram;
use App\Policies\DiagramPolicy;
use App\Repositories\DiagramRepository;
use App\Repositories\DiagramRepositoryInterface;
use App\Services\PlanLimitService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\View;
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

        View::share([
            'diagramLimit' => PlanLimitService::DIAGRAM_LIMIT,
            'exportWeeklyLimit' => PlanLimitService::EXPORT_WEEKLY_LIMIT,
            'schemaDoctorWeeklyLimit' => PlanLimitService::SCHEMA_DOCTOR_WEEKLY_LIMIT,
        ]);

        // Alpine musl + pcntl_async_signals breaks the first SSL stream_socket_client call.
        // One throwaway attempt initializes OpenSSL state so subsequent connections succeed.
        // In simple words, some absolute BS happened to queue-emails docker container on prod, and this is the only solution that fixed it
        if ($this->app->runningInConsole()) {
            Queue::before(function () {
                static $warmedUp = false;
                if ($warmedUp) {
                    return;
                }
                $warmedUp = true;
                $host = config('mail.mailers.smtp.host');
                $port = config('mail.mailers.smtp.port');
                if ($host && $port) {
                    $conn = @stream_socket_client("ssl://{$host}:{$port}", $e, $es, 5, STREAM_CLIENT_CONNECT);
                    if ($conn) {
                        fclose($conn);
                    }
                }
            });
        }
    }
}
