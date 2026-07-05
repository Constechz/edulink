<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\School;

class AdmissionsApprovedWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $parentUser;
    public $parentPassword;
    public $studentUser;
    public $studentPassword;
    public $school;

    /**
     * Create a new message instance.
     */
    public function __construct(User $parentUser, string $parentPassword, User $studentUser, string $studentPassword, School $school)
    {
        $this->parentUser = $parentUser;
        $this->parentPassword = $parentPassword;
        $this->studentUser = $studentUser;
        $this->studentPassword = $studentPassword;
        $this->school = $school;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Welcome to {$this->school->name} - Portal Access Granted")
                    ->view('emails.admissions_approved');
    }
}
