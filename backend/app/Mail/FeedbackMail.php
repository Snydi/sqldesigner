<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public function __construct(
        public readonly string $body,
        public readonly ?string $senderEmail,
    ) {
        $this->subject = $senderEmail
            ? "Feedback from $senderEmail"
            : 'Anonymous Feedback';
    }

    public function build(): self
    {
        return $this->view('mail.feedback')->subject($this->subject);
    }
}
