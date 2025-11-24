<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    private string $userEmail;

    public function __construct($email)
    {
        $this->subject = 'New user registration';
        $this->userEmail = $email;
    }

    public function build(): NewUserRegistrationMail
    {
        return $this->view('mail.admin.newUserRegistration')
            ->with(['userEmail' => $this->userEmail])
            ->subject($this->subject);
    }
}
