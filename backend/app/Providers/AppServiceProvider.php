<?php

namespace App\Providers;

use App\Models\Diagram;
use App\Policies\DiagramPolicy;
use App\Repositories\DiagramRepository;
use App\Repositories\DiagramRepositoryInterface;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DiagramRepositoryInterface::class, DiagramRepository::class);

        $this->app->singleton('mail.manager', function ($app) {
            return new class($app) extends MailManager {
                protected function configureSmtpTransport(EsmtpTransport $transport, array $config): EsmtpTransport
                {
                    parent::configureSmtpTransport($transport, $config);
                    $stream = $transport->getStream();
                    if ($stream instanceof SocketStream && isset($config['stream'])) {
                        $stream->setStreamOptions($config['stream']);
                    }
                    return $transport;
                }
            };
        });
    }

    public function boot(): void
    {
        Gate::policy(Diagram::class, DiagramPolicy::class);

        Event::listen(MessageSent::class, function () {
            $transport = app('mailer')->getSymfonyTransport();
            if ($transport instanceof EsmtpTransport) {
                try {
                    $transport->stop();
                } catch (\Throwable) {
                }
            }
        });
    }
}
