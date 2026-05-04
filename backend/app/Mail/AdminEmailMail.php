<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $emailSubject,
        public readonly string $body,
    ) {
        $this->subject = $emailSubject;
    }

    public function build(): self
    {
        return $this->view('mail.admin-email')
            ->subject($this->emailSubject)
            ->with(['subject' => $this->emailSubject]);
    }
}
