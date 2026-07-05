<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\School;

class SchoolNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customSubject;
    public $customBody;
    public $school;

    /**
     * Create a new message instance.
     */
    public function __construct(string $subject, string $body, School $school)
    {
        $this->customSubject = $subject;
        $this->customBody = $body;
        $this->school = $school;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->customSubject)
                    ->view('emails.school_notice');
    }
}
