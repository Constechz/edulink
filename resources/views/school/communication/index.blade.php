@extends('layouts.app')

@section('title', 'Notice Blasts & SMS | EduLink')
@section('header_title', 'School Communication Center')

@section('content')
<div class="container-fluid p-0">
    <!-- Include Quill WYSIWYG Editor Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">

    <!-- Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 glass-card p-3" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 glass-card p-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i><strong>Action Failed:</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics Dashboard Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-4 p-3">
                    <i class="bi bi-broadcast fs-3"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Total Campaigns</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ $totalSent }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 d-flex align-items-center gap-3">
                <div class="bg-success bg-opacity-10 text-success rounded-4 p-3">
                    <i class="bi bi-envelope-paper-fill fs-3"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Email Blasts</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ $emailCount }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 d-flex align-items-center gap-3">
                <div class="bg-info bg-opacity-10 text-info rounded-4 p-3">
                    <i class="bi bi-chat-left-text-fill fs-3"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">SMS Notices</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ $smsCount }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-10 text-warning rounded-4 p-3">
                    <i class="bi bi-people-fill fs-3"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Total Recipients</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ $totalRecipients }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        <!-- Left Panel: Blaster & Templates Tab Layout -->
        <div class="col-lg-5">
            <div class="glass-card p-4">
                <!-- Nav Tabs -->
                <ul class="nav nav-pills nav-fill mb-4" id="communicationTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold" id="compose-tab" data-bs-toggle="tab" data-bs-target="#compose" type="button" role="tab" aria-controls="compose" aria-selected="true">
                            <i class="bi bi-send me-2"></i>Compose Blast
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="templates-tab" data-bs-toggle="tab" data-bs-target="#templates" type="button" role="tab" aria-controls="templates" aria-selected="false">
                            <i class="bi bi-file-earmark-text me-2"></i>Manage Templates
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="communicationTabContent">
                    <!-- Tab 1: Compose Form -->
                    <div class="tab-pane fade show active" id="compose" role="tabpanel" aria-labelledby="compose-tab">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-broadcast me-2 text-primary"></i>Launch Broadcast Blast</h6>
                        <p class="text-muted small">Send instant notifications via Email or SMS. System announcements are auto-published on student/parent portals.</p>
                        
                        <form action="{{ route('school.communication.send-blast') }}" method="POST" id="broadcastForm">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Delivery Channel</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="channel" id="channel_email" value="email" checked onchange="toggleChannelFields('email')">
                                        <label class="form-check-label small" for="channel_email">
                                            <i class="bi bi-envelope-fill text-primary me-1"></i>Email Blast
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="channel" id="channel_sms" value="sms" onchange="toggleChannelFields('sms')">
                                        <label class="form-check-label small" for="channel_sms">
                                            <i class="bi bi-chat-left-text-fill text-success me-1"></i>SMS Notice
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="target_audience" class="form-label small fw-semibold">Target Audience</label>
                                <select class="form-select rounded-3" id="target_audience" name="target_audience" required>
                                    <option value="all">All Stakeholders (Staff, Students & Parents)</option>
                                    <option value="staff">Staff Members Only</option>
                                    <option value="students">Students Only</option>
                                    <option value="parents">Guardians / Parents Only</option>
                                </select>
                            </div>

                            <div class="mb-3" id="subject_group">
                                <label for="subject" class="form-label small fw-semibold">Email Subject</label>
                                <input type="text" class="form-control rounded-3" id="subject" name="subject" placeholder="e.g. End of Term Examination Timetable" value="{{ old('subject') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Message Body</label>
                                
                                <!-- Plain Text Editor (For SMS) -->
                                <textarea class="form-control rounded-3 d-none" id="body" name="body" rows="6" placeholder="Type your SMS notice body here...">{{ old('body') }}</textarea>
                                
                                <!-- Quill WYSIWYG HTML Editor (For Email) -->
                                <div id="quill_editor_wrapper" class="bg-white rounded-3" style="min-height: 180px;">
                                    <div id="quill_editor"></div>
                                </div>

                                <div class="form-text text-muted small d-flex justify-content-between mt-1">
                                    <span id="char_counter_msg">Email blasts can contain HTML rich content.</span>
                                    <span id="char_count" class="d-none">0 characters</span>
                                </div>
                            </div>

                            <!-- Optional Templates quick filler -->
                            @if(!$templates->isEmpty())
                                <div class="mb-3 bg-light p-3 rounded-4" id="quick_templates_container">
                                    <label class="form-label small fw-semibold d-block"><i class="bi bi-lightning-charge-fill me-1 text-warning"></i>Load Template Quick-Fill</label>
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @foreach($templates as $tmpl)
                                            <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 rounded-pill small" style="font-size: 0.75rem;" onclick="loadTemplate('{{ $tmpl->channel }}', '{{ addslashes($tmpl->subject ?? '') }}', '{{ addslashes($tmpl->body) }}')">
                                                {{ $tmpl->name }} ({{ strtoupper($tmpl->channel) }})
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                                <i class="bi bi-send me-1"></i>Dispatch Broadcast Campaign
                            </button>
                        </form>
                    </div>

                    <!-- Tab 2: Templates Management -->
                    <div class="tab-pane fade" id="templates" role="tabpanel" aria-labelledby="templates-tab">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-text-fill me-2 text-info"></i>Manage Message Templates</h6>
                        <p class="text-muted small">Save template notices you send regularly for quick reuse.</p>

                        <!-- Add Template Form -->
                        <form action="{{ route('school.communication.templates.store') }}" method="POST" class="mb-4 p-3 bg-light rounded-4">
                            @csrf
                            <h7 class="fw-bold text-secondary d-block mb-3 small"><i class="bi bi-plus-circle me-1"></i>Create New Template</h7>
                            
                            <div class="mb-2">
                                <label for="template_name" class="form-label small fw-semibold">Template Name</label>
                                <input type="text" class="form-control form-control-sm rounded-2" id="template_name" name="name" required placeholder="e.g. End of Term Announcement">
                            </div>

                            <div class="mb-2">
                                <label for="template_channel" class="form-label small fw-semibold">Delivery Channel</label>
                                <select class="form-select form-select-sm rounded-2" id="template_channel" name="channel" required onchange="toggleTemplateSubject(this.value)">
                                    <option value="email">Email Template</option>
                                    <option value="sms">SMS Template</option>
                                </select>
                            </div>

                            <div class="mb-2" id="template_subject_group">
                                <label for="template_subject" class="form-label small fw-semibold">Email Subject</label>
                                <input type="text" class="form-control form-control-sm rounded-2" id="template_subject" name="subject" placeholder="e.g. End of Term Notice">
                            </div>

                            <div class="mb-3">
                                <label for="template_body" class="form-label small fw-semibold">Template Body</label>
                                <textarea class="form-control form-control-sm rounded-2" id="template_body" name="body" rows="4" required placeholder="Type template body text here..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-xs btn-primary rounded-2 py-1 px-3 fw-semibold">
                                <i class="bi bi-save me-1"></i>Save Template
                            </button>
                        </form>

                        <!-- Templates List -->
                        <h7 class="fw-bold text-dark d-block mb-2 small"><i class="bi bi-list-task me-1"></i>Existing Templates</h7>
                        @if($templates->isEmpty())
                            <div class="text-center py-4 bg-light rounded-4 text-muted small">
                                No saved templates found. Create one above!
                            </div>
                        @else
                            <div class="d-flex flex-column gap-2" style="max-height: 250px; overflow-y: auto;">
                                @foreach($templates as $tmpl)
                                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded-3 border-start border-3 border-info">
                                        <div>
                                            <span class="fw-bold text-dark small">{{ $tmpl->name }}</span>
                                            <span class="badge bg-secondary text-dark ms-2" style="font-size: 0.65rem;">{{ strtoupper($tmpl->channel) }}</span>
                                        </div>
                                        <form action="{{ route('school.communication.templates.destroy', $tmpl->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this template?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0" title="Delete Template">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: History Outbox list -->
        <div class="col-lg-7">
            <div class="glass-card p-4 h-100">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Blast Outbox</h5>
                
                @if($messages->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-mailbox fs-1 d-block mb-3 opacity-50"></i>
                        <p class="mb-0">No broadcast notice messages sent yet.</p>
                    </div>
                @else
                    <div class="d-flex flex-column gap-3" style="max-height: 650px; overflow-y: auto; padding-right: 5px;">
                        @foreach($messages as $msg)
                            <div class="p-3 bg-light rounded-4 border-start border-4 border-primary shadow-xs">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-secondary text-dark text-uppercase small fw-bold">
                                        {{ $msg->channel }}
                                    </span>
                                    <span class="text-muted small">
                                        {{ date('M d, Y h:i A', strtotime($msg->created_at)) }}
                                    </span>
                                </div>
                                @if($msg->subject)
                                    <h6 class="fw-bold text-dark mb-1">{{ $msg->subject }}</h6>
                                @endif
                                <p class="small text-muted mb-2 text-wrap" style="white-space: pre-wrap;">{{ strip_tags($msg->body) }}</p>
                                
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top text-muted small" style="font-size: 0.75rem;">
                                    <span>
                                        <i class="bi bi-people me-1"></i>Recipient Count: <strong>{{ $msg->recipients->count() }}</strong>
                                    </span>
                                    <div>
                                        <button type="button" class="btn btn-outline-dark btn-xs rounded-2 px-2 py-1 me-2" data-bs-toggle="modal" data-bs-target="#trackingModal{{ $msg->id }}">
                                            <i class="bi bi-eye"></i> View Recipients
                                        </button>
                                        <span class="text-success fw-semibold">
                                            <i class="bi bi-check-circle-fill me-1"></i>Dispatched
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal for Recipient Tracking Details -->
                            <div class="modal fade" id="trackingModal{{ $msg->id }}" tabindex="-1" aria-labelledby="trackingModalLabel{{ $msg->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow-lg">
                                        <div class="modal-header border-bottom p-3">
                                            <h6 class="modal-title fw-bold text-dark" id="trackingModalLabel{{ $msg->id }}">
                                                <i class="bi bi-people-fill text-primary me-2"></i>Recipient Delivery Log: {{ $msg->subject ?: 'Notice Blast' }}
                                            </h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 bg-light">
                                            <div class="p-3 bg-white border rounded-4 overflow-auto" style="max-height: 400px;">
                                                <table class="table table-hover align-middle table-sm small">
                                                    <thead>
                                                        <tr>
                                                            <th>Recipient User</th>
                                                            <th>Contact Address</th>
                                                            <th>Delivery Mode</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($msg->recipients as $recipient)
                                                            <tr>
                                                                <td>
                                                                    <span class="fw-bold text-dark">{{ $recipient->user ? $recipient->user->name : 'N/A' }}</span>
                                                                </td>
                                                                <td class="text-muted">
                                                                    {{ $msg->channel === 'email' ? $recipient->recipient_email : $recipient->recipient_phone }}
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-light text-secondary border px-2 py-1">{{ strtoupper($msg->channel) }}</span>
                                                                </td>
                                                                <td>
                                                                    <span class="badge {{ $recipient->status === 'sent' ? 'bg-success' : 'bg-danger' }}">
                                                                        {{ $recipient->status }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<!-- Include Quill WYSIWYG Editor JS Library -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<script>
    let quill;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Quill Editor
        quill = new Quill('#quill_editor', {
            theme: 'snow',
            placeholder: 'Compose your rich text notice content here...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'clean']
                ]
            }
        });

        // Sync initial body textarea content (if any) back to Quill
        const bodyTextarea = document.getElementById('body');
        if (bodyTextarea && bodyTextarea.value.trim() !== '') {
            quill.root.innerHTML = bodyTextarea.value;
        }

        // On Form Submission, sync Quill content to body textarea
        const form = document.getElementById('broadcastForm');
        form.addEventListener('submit', function() {
            const channel = document.querySelector('input[name="channel"]:checked').value;
            if (channel === 'email') {
                bodyTextarea.value = quill.root.innerHTML;
            }
        });

        // Set initial visibility
        toggleChannelFields('email');
    });

    function toggleChannelFields(channel) {
        const subjectGroup = document.getElementById('subject_group');
        const subjectInput = document.getElementById('subject');
        const quillWrapper = document.getElementById('quill_editor_wrapper');
        const textareaBody = document.getElementById('body');
        const charMsg = document.getElementById('char_counter_msg');
        const charCount = document.getElementById('char_count');
        
        if (channel === 'email') {
            subjectGroup.classList.remove('d-none');
            subjectInput.setAttribute('required', 'required');
            quillWrapper.classList.remove('d-none');
            textareaBody.classList.add('d-none');
            charMsg.innerText = 'Email blasts can contain HTML content and have no character limits.';
            charCount.classList.add('d-none');
        } else {
            subjectGroup.classList.add('d-none');
            subjectInput.removeAttribute('required');
            quillWrapper.classList.add('d-none');
            textareaBody.classList.remove('d-none');
            textareaBody.setAttribute('required', 'required');
            charMsg.innerText = 'SMS limit is 160 characters per credit unit.';
            charCount.classList.remove('d-none');
            updateCharCount();
        }
    }

    // Load templates quick filler
    function loadTemplate(channel, subject, body) {
        // Match compose form channel with template channel
        if (channel === 'email') {
            document.getElementById('channel_email').checked = true;
            toggleChannelFields('email');
            quill.root.innerHTML = body;
            const subjectInput = document.getElementById('subject');
            if (subjectInput) {
                subjectInput.value = subject;
            }
        } else {
            document.getElementById('channel_sms').checked = true;
            toggleChannelFields('sms');
            document.getElementById('body').value = body;
            updateCharCount();
        }
    }

    // Character counter for message body (SMS channel)
    const bodyTextarea = document.getElementById('body');
    const countDisplay = document.getElementById('char_count');

    function updateCharCount() {
        const len = bodyTextarea.value.length;
        countDisplay.innerText = len + ' / 160 characters';
        if (len > 160) {
            countDisplay.classList.add('text-danger');
        } else {
            countDisplay.classList.remove('text-danger');
        }
    }

    if (bodyTextarea && countDisplay) {
        bodyTextarea.addEventListener('input', updateCharCount);
    }

    // Quick templates channel config
    function toggleTemplateSubject(channel) {
        const subjectGroup = document.getElementById('template_subject_group');
        const subjectInput = document.getElementById('template_subject');
        if (channel === 'email') {
            subjectGroup.classList.remove('d-none');
            subjectInput.setAttribute('required', 'required');
        } else {
            subjectGroup.classList.add('d-none');
            subjectInput.removeAttribute('required');
        }
    }
</script>
@endsection
