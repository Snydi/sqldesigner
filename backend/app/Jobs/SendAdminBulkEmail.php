<?php

namespace App\Jobs;

use App\Mail\AdminEmailMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAdminBulkEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;
    public int $backoff = 60;

    public function __construct(
        private readonly string $email,
        private readonly string $subject,
        private readonly string $body,
    ) {
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        Mail::to($this->email)->send(new AdminEmailMail($this->subject, $this->body));
    }
}
