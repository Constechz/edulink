<?php

namespace App\Jobs;

use App\Models\School;
use App\Models\User;
use App\Mail\SchoolApprovedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AutoApproveSchoolAndUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $school;

    /**
     * Create a new job instance.
     */
    public function __construct(School $school)
    {
        $this->school = $school;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Refresh school from DB to check if it's already active/approved
        $this->school->refresh();

        if (!$this->school->is_active) {
            Log::info("Auto-approving school ID: {$this->school->id} and associated users.");

            $this->school->update([
                'is_active' => true,
                'subscription_status' => 'active',
                'trial_ends_at' => null,
            ]);

            // Activate user(s) belonging to the school
            User::withoutGlobalScopes()->where('school_id', $this->school->id)->update(['is_active' => true]);

            // Send approval confirmation email to the school owner
            try {
                Mail::to($this->school->owner_email)->send(new SchoolApprovedMail($this->school));
                Log::info("School approval email sent to: {$this->school->owner_email}");
            } catch (\Exception $e) {
                Log::error("Failed to send school approval email: " . $e->getMessage());
            }
        } else {
            Log::info("School ID: {$this->school->id} is already active. Skipping auto-approval.");
        }
    }
}
