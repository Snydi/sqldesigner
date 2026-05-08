<?php

namespace App\Jobs;

use App\Mail\Admin\NewUserRegistrationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewUserRegistrationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;
    public int $backoff = 60;

    public function __construct(private readonly string $userEmail)
    {
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        Mail::to(config('mail.from.address'))->send(new NewUserRegistrationMail($this->userEmail));
    }
}
