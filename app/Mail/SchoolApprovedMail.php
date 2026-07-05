<?php

namespace App\Mail;

use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SchoolApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $school;

    /**
     * Create a new message instance.
     */
    public function __construct(School $school)
    {
        $this->school = $school;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Congratulations! Your School Has Been Approved!')
                    ->view('emails.school_approved');
    }
}
