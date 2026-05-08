<?php

namespace App\Jobs;

use App\Mail\FeedbackMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendFeedbackEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;
    public int $backoff = 60;

    public function __construct(
        private readonly string $body,
        private readonly ?string $senderEmail,
    ) {
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        Mail::to(config('mail.smtp.username'))->send(new FeedbackMail($this->body, $this->senderEmail));
    }
}
