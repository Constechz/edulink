<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\MessageTemplate;
use App\Models\SmsDeliveryLog;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunicationController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $messages = Message::where('school_id', $schoolId)
            ->with(['recipients'])
            ->orderBy('created_at', 'desc')
            ->get();

        $templates = MessageTemplate::where('school_id', $schoolId)->get();

        // Calculate statistics
        $totalSent = $messages->count();
        $emailCount = $messages->where('channel', 'email')->count();
        $smsCount = $messages->where('channel', 'sms')->count();
        
        $totalRecipients = 0;
        foreach ($messages as $msg) {
            $totalRecipients += $msg->recipients->count();
        }

        return view('school.communication.index', compact(
            'messages', 'templates', 'totalSent', 'emailCount', 'smsCount', 'totalRecipients'
        ));
    }

    public function sendBlast(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'channel' => 'required|in:sms,email',
            'target_audience' => 'required|in:all,students,parents,staff',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
        ]);

        // Resolve recipients based on target audience
        $usersQuery = User::where('school_id', $schoolId)->where('is_active', true);

        if ($request->target_audience === 'staff') {
            $usersQuery->whereHas('role', function ($q) {
                $q->whereIn('slug', ['school-admin', 'headteacher', 'hod', 'teacher']);
            });
        } elseif ($request->target_audience === 'students') {
            $usersQuery->whereHas('role', function ($q) {
                $q->where('slug', 'student');
            });
        } elseif ($request->target_audience === 'parents') {
            $usersQuery->whereHas('role', function ($q) {
                $q->where('slug', 'parent');
            });
        }

        $recipients = $usersQuery->get();

        if ($recipients->isEmpty()) {
            return redirect()->back()->withErrors(['target_audience' => 'No active user accounts found matching the target audience.']);
        }

        $school = \App\Models\School::findOrFail($schoolId);

        DB::transaction(function() use ($schoolId, $request, $recipients, $school) {
            // 1. Create Message
            $message = Message::create([
                'school_id' => $schoolId,
                'sender_user_id' => $request->user()->id,
                'channel' => $request->channel,
                'subject' => $request->channel === 'email' ? ($request->subject ?? 'School Notice') : null,
                'body' => $request->body,
                'status' => 'completed'
            ]);

            // 2. Loop and record delivery logs
            foreach ($recipients as $user) {
                MessageRecipient::create([
                    'message_id' => $message->id,
                    'recipient_user_id' => $user->id,
                    'recipient_phone' => $user->phone ?? '+233240000000',
                    'recipient_email' => $user->email,
                    'status' => 'sent'
                ]);

                if ($request->channel === 'sms') {
                    // Send SMS via unified service
                    $smsService = new \App\Services\SmsService();
                    $schoolSenderId = $school->sms_gateway_config['sender_id'] ?? null;
                    $smsResult = $smsService->send($user->phone ?? '+233240000000', $request->body, $schoolSenderId);

                    // Record SMS log and consume credits
                    SmsDeliveryLog::create([
                        'school_id' => $schoolId,
                        'phone_number' => $user->phone ?? '+233240000000',
                        'message_body' => $request->body,
                        'credits_used' => 1,
                        'status' => $smsResult['success'] ? 'delivered' : 'failed',
                        'reference' => $smsResult['reference'] ?? 'TXN-' . mt_rand(100000, 999999)
                    ]);
                } elseif ($request->channel === 'email') {
                    try {
                        \Illuminate\Support\Facades\Mail::to($user->email)->send(
                            new \App\Mail\SchoolNoticeMail($request->subject ?? 'School Notice', $request->body, $school)
                        );
                    } catch (\Exception $e) {
                        \App\Models\EmailLog::create([
                            'recipient_email' => $user->email,
                            'subject' => $request->subject ?? 'School Notice',
                            'body' => $request->body,
                            'status' => 'failed',
                            'error_message' => $e->getMessage(),
                        ]);
                    }
                }
            }

            // Create system announcement
            DB::table('announcements')->insert([
                'school_id' => $schoolId,
                'title' => $request->channel === 'email' ? ($request->subject ?? 'New Notification') : 'Notice Blast',
                'content' => $request->body,
                'target_audience' => $request->target_audience,
                'is_pinned' => false,
                'created_by' => $request->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->back()->with('success', 'Communication campaign blast dispatched successfully.');
    }

    /**
     * Save a new template.
     */
    public function storeTemplate(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'name' => 'required|string|max:255',
            'channel' => 'required|in:sms,email',
            'subject' => 'nullable|required_if:channel,email|string|max:255',
            'body' => 'required|string',
        ]);

        MessageTemplate::create([
            'school_id' => $schoolId,
            'name' => $request->name,
            'channel' => $request->channel,
            'subject' => $request->channel === 'email' ? $request->subject : null,
            'body' => $request->body,
        ]);

        return redirect()->back()->with('success', 'Message template created successfully.');
    }

    /**
     * Delete a template.
     */
    public function destroyTemplate(Request $request, $id)
    {
        $schoolId = $request->user()->school_id;
        $template = MessageTemplate::where('school_id', $schoolId)->findOrFail($id);
        $template->delete();

        return redirect()->back()->with('success', 'Message template deleted successfully.');
    }
}
