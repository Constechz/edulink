<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SuperAdminCustomMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customSubject;
    public $customBody;

    /**
     * Create a new message instance.
     */
    public function __construct(string $subject, string $body)
    {
        $this->customSubject = $subject;
        $this->customBody = $body;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->customSubject)
                    ->view('emails.super_admin_custom');
    }
}
