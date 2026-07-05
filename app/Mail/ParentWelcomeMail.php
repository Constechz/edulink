<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\School;

class ParentWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $school;
    public $temporaryPassword;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, School $school, string $temporaryPassword)
    {
        $this->user = $user;
        $this->school = $school;
        $this->temporaryPassword = $temporaryPassword;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Welcome to {$this->school->name} - Parent Portal Access")
                    ->view('emails.parent_welcome');
    }
}
