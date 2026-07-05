@extends('layouts.app')

@section('title', $course->title . ' | LMS')
@section('header_title', 'Course Curriculum Workspace')

@section('content')
<div class="container-fluid p-0">
    <!-- Main Course card header -->
    <div class="glass-card border-0 rounded-4 overflow-hidden mb-4 p-4 position-relative" style="background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.08) 0%, rgba(var(--bs-warning-rgb), 0.05) 100%);">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('school.lms.courses.index') }}" class="text-decoration-none text-primary small"><i class="bi bi-journal-album me-1"></i>LMS Catalog</a></li>
                <li class="breadcrumb-item active text-dark small" aria-current="page">Course Details</li>
            </ol>
        </nav>

        <div class="d-flex flex-column flex-md-row align-items-md-center gap-4">
            @if($course->thumbnail)
                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="rounded-4 object-fit-cover shadow-sm border border-light" style="width: 120px; height: 120px; min-width: 120px;">
            @else
                <div class="rounded-4 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary shadow-sm border border-light" style="width: 120px; height: 120px; min-width: 120px;">
                    <i class="bi bi-book fs-1"></i>
                </div>
            @endif
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary mb-2 px-3 py-2 fs-7 rounded-3"><i class="bi bi-tag-fill me-1"></i>{{ $course->subject->name ?? 'N/A' }}</span>
                <h2 class="fw-black mb-2 text-dark">{{ $course->title }}</h2>
                <p class="text-secondary mb-0 max-w-2xl lh-relaxed" style="font-size: 0.95rem;">{{ $course->description ?? 'No curriculum summary description registered for this learning workspace.' }}</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Lessons and Quizzes list -->
        <div class="col-md-8">
            <!-- Lessons List -->
            <div class="glass-card border-0 rounded-4 p-4 mb-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-list-ol me-2 text-primary"></i>Curriculum Syllabus</h5>
                        <p class="text-muted small mb-0">Step-by-step reading modules & study materials</p>
                    </div>
                    @if($isStaff)
                        <button type="button" class="btn btn-sm btn-primary rounded-3 px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createLessonModal">
                            <i class="bi bi-plus-lg me-1"></i> Add Lesson
                        </button>
                    @endif
                </div>

                @if($course->lessons->isEmpty())
                    <div class="text-center py-5 text-muted bg-light bg-opacity-10 rounded-4 border border-dashed border-light-subtle">
                        <i class="bi bi-journal-text fs-1 mb-2 text-secondary d-block"></i>
                        <span class="fw-semibold">No syllabus modules defined yet</span>
                        <p class="small text-muted mb-0">Lessons added by staff will appear in this workspace layout.</p>
                    </div>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach($course->lessons as $lesson)
                            @php
                                $completed = in_array($lesson->id, $completedLessonIds);
                            @endphp
                            <div class="card bg-glass border-light-subtle rounded-4 transition-all hover-translate-y shadow-xs">
                                <div class="card-body p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="badge rounded-circle p-0 d-flex align-items-center justify-content-center {{ $completed ? 'bg-success text-white' : 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20' }}" style="width: 38px; height: 38px; min-width: 38px; font-weight: 700; font-size: 1rem;">
                                            @if($completed) <i class="bi bi-check-lg"></i> @else {{ $loop->iteration }} @endif
                                        </span>
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark">{{ $lesson->title }}</h6>
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="badge bg-secondary bg-opacity-10 text-dark small px-2 py-1"><i class="bi bi-file-earmark-arrow-down me-1 text-info"></i>{{ $lesson->resources->count() }} Attachments</span>
                                                <span class="badge bg-primary bg-opacity-10 text-primary small px-2 py-1"><i class="bi bi-clock me-1"></i>Module {{ $loop->iteration }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($completed)
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-3 small"><i class="bi bi-check-circle-fill me-1"></i> Completed</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-dark px-3 py-2 rounded-3 small">Not Read</span>
                                        @endif
                                        <a href="{{ route('school.lms.lessons.show', $lesson->id) }}" class="btn btn-sm btn-primary rounded-3 px-3 shadow-xs"><i class="bi bi-play-circle me-1"></i> Start Lesson</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- TIMED QUIZZES SECTION -->
            <div class="glass-card border-0 rounded-4 p-4 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-check2-square me-2 text-warning"></i>Timed Assessments</h5>
                        <p class="text-muted small mb-0">Test your comprehension with graded quizzes</p>
                    </div>
                    @if($isStaff)
                        <button type="button" class="btn btn-sm btn-warning text-dark rounded-3 px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createQuizModal">
                            <i class="bi bi-plus-lg me-1"></i> Create Quiz
                        </button>
                    @endif
                </div>

                @if($quizzes->isEmpty())
                    <div class="text-center py-4 text-muted bg-light bg-opacity-10 rounded-4 border border-dashed border-light-subtle">
                        <i class="bi bi-award fs-1 mb-2 text-secondary d-block"></i>
                        <span class="fw-semibold">No active tests for this course</span>
                    </div>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach($quizzes as $quiz)
                            @php
                                $attempt = DB::table('lms_quiz_attempts')
                                    ->where('quiz_id', $quiz->id)
                                    ->orderBy('attempted_at', 'desc')
                                    ->first();
                            @endphp
                            <div class="card bg-glass border-light-subtle rounded-4 transition-all hover-translate-y shadow-xs">
                                <div class="card-body p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark"><i class="bi bi-card-list me-2 text-warning"></i>{{ $quiz->title }}</h6>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="badge bg-secondary bg-opacity-10 text-dark small px-2 py-1"><i class="bi bi-question-circle me-1 text-primary"></i>{{ $quiz->questions->count() }} Questions</span>
                                            <span class="badge bg-secondary bg-opacity-10 text-dark small px-2 py-1"><i class="bi bi-percent me-1 text-success"></i>Pass Mark: {{ $quiz->passing_percentage }}%</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($attempt)
                                            <span class="badge {{ $attempt->is_passed ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} px-3 py-2 rounded-3 small">
                                                Last Score: {{ $attempt->score }}% ({{ $attempt->is_passed ? 'Passed' : 'Failed' }})
                                            </span>
                                        @endif
                                        @if($isStaff)
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#addQuestionModal{{ $quiz->id }}">
                                                <i class="bi bi-plus-lg me-1"></i> Add Question
                                            </button>
                                        @endif
                                        <a href="{{ route('school.lms.quizzes.show', $quiz->id) }}" class="btn btn-sm btn-warning text-dark rounded-3 px-3 shadow-xs"><i class="bi bi-pencil-square me-1"></i> Take Test</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Right sidebar (Instructor Details & Course Forums) -->
        <div class="col-md-4">
            <div class="glass-card p-4 mb-4 text-center border-0 shadow-sm relative overflow-hidden">
                <div class="position-absolute top-0 start-0 w-100 h-2 bg-primary"></div>
                <h5 class="fw-bold text-start mb-3 text-dark">Course Instructor</h5>
                <div class="d-flex flex-column align-items-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($course->teacher->name) }}&background=003366&color=fff&size=100" class="rounded-circle mb-3 border shadow-sm" alt="{{ $course->teacher->name }}">
                    <h6 class="fw-bold text-dark mb-1 fs-5">{{ $course->teacher->name }}</h6>
                    <p class="text-secondary small mb-3"><i class="bi bi-envelope me-1 text-primary"></i>{{ $course->teacher->email }}</p>
                    <a href="mailto:{{ $course->teacher->email }}" class="btn btn-sm btn-outline-primary w-100 rounded-3"><i class="bi bi-chat-dots me-1"></i> Contact Instructor</a>
                </div>
            </div>

            <!-- Course Forum Q&A -->
            <div class="glass-card p-4 border-0 shadow-sm mb-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-chat-left-text me-2 text-primary"></i>Discussion Forum</h5>
                @if($forums->isEmpty())
                    @php
                        $seededForum = DB::table('lms_forums')->insertGetId([
                            'course_id' => $course->id,
                            'title' => 'General Q&A',
                            'description' => 'Discussions about ' . $course->title,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $forums = \App\Models\LmsForum::where('id', $seededForum)->get();
                    @endphp
                @endif

                @foreach($forums as $forum)
                    @php
                        $posts = DB::table('lms_forum_posts')
                            ->where('forum_id', $forum->id)
                            ->join('users', 'lms_forum_posts.user_id', '=', 'users.id')
                            ->select('lms_forum_posts.*', 'users.name as user_name')
                            ->orderBy('lms_forum_posts.created_at', 'desc')
                            ->get();
                    @endphp
                    <style>
                        /* Custom Chat Container */
                        .chat-feed-container {
                            max-height: 380px;
                            overflow-y: auto;
                            padding-right: 4px;
                        }
                        .chat-feed-container::-webkit-scrollbar {
                            width: 6px;
                        }
                        .chat-feed-container::-webkit-scrollbar-track {
                            background: transparent;
                        }
                        .chat-feed-container::-webkit-scrollbar-thumb {
                            background: rgba(0, 0, 0, 0.1);
                            border-radius: 10px;
                        }
                        [data-bs-theme="dark"] .chat-feed-container::-webkit-scrollbar-thumb {
                            background: rgba(255, 255, 255, 0.15);
                        }
                        
                        /* Message Bubble Styles */
                        .message-bubble {
                            position: relative;
                            padding: 12px 14px;
                            border-radius: 14px;
                            margin-bottom: 10px;
                            border: 1px solid rgba(0, 0, 0, 0.05);
                            background-color: rgba(248, 250, 252, 0.7);
                        }
                        [data-bs-theme="dark"] .message-bubble {
                            background-color: rgba(30, 41, 59, 0.4);
                            border-color: rgba(255, 255, 255, 0.05);
                        }
                        
                        /* Instructor Bubble (Uses soft border accent and tinted background) */
                        .message-bubble.instructor-bubble {
                            border-left: 4px solid var(--primary-color) !important;
                            background-color: rgba(0, 51, 102, 0.04) !important;
                        }
                        [data-bs-theme="dark"] .message-bubble.instructor-bubble {
                            background-color: rgba(88, 166, 255, 0.08) !important;
                            border-left-color: var(--primary-color) !important;
                        }

                        /* Avatar and Header Layout */
                        .chat-meta {
                            font-size: 0.78rem;
                            font-weight: 600;
                            margin-bottom: 4px;
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                        }
                        .chat-user-name {
                            color: var(--text-main);
                        }
                        .instructor-bubble .chat-user-name {
                            color: var(--primary-color) !important;
                        }
                        .chat-time {
                            font-size: 0.65rem;
                            color: var(--text-muted);
                            font-weight: 400;
                        }
                        .chat-content {
                            font-size: 0.84rem;
                            line-height: 1.5;
                            color: var(--text-main);
                            word-break: break-word;
                            margin-bottom: 0;
                        }
                        
                        /* Form custom styles */
                        .forum-send-form .input-group {
                            border-radius: 12px;
                            overflow: hidden;
                            border: 1px solid rgba(0, 0, 0, 0.08);
                            background: #fff;
                        }
                        [data-bs-theme="dark"] .forum-send-form .input-group {
                            border-color: rgba(255, 255, 255, 0.1);
                            background: rgba(30, 41, 59, 0.6);
                        }
                        .forum-send-form input {
                            border: none !important;
                            box-shadow: none !important;
                            background: transparent !important;
                            color: var(--text-main) !important;
                            font-size: 0.88rem;
                            padding: 10px 14px;
                        }
                        .forum-send-form button {
                            background-color: var(--primary-color);
                            color: #fff;
                            border: none;
                            padding: 0 16px;
                            transition: opacity 0.2s ease;
                        }
                        .forum-send-form button:hover {
                            opacity: 0.9;
                        }
                    </style>

                    <div class="p-3 bg-light bg-opacity-5 rounded-4 mb-3 border border-light-subtle">
                        <h6 class="fw-bold mb-1 text-dark"><i class="bi bi-hash text-primary me-1"></i>{{ $forum->title }}</h6>
                        <p class="text-secondary small mb-3">{{ $forum->description }}</p>

                        <!-- Post new message -->
                        <form action="{{ route('school.lms.forums.post.store', $forum->id) }}" method="POST" class="mb-3 forum-send-form">
                            @csrf
                            <div class="input-group shadow-xs">
                                <input type="text" name="content" class="form-control" placeholder="Write a message/question..." required>
                                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-send-fill"></i></button>
                            </div>
                        </form>

                        <!-- Messages feed -->
                        <div class="chat-feed-container d-flex flex-column animate-feed">
                            @if($posts->isEmpty())
                                <div class="text-center py-4 text-muted small">
                                    <i class="bi bi-chat-quote fs-4 d-block mb-1"></i>
                                    No messages posted yet. Start the conversation!
                                </div>
                            @else
                                @foreach($posts as $post)
                                    @php
                                        $isTeacherPost = ($post->user_id === $course->teacher_id);
                                    @endphp
                                    <div class="message-bubble {{ $isTeacherPost ? 'instructor-bubble' : '' }}">
                                        <div class="chat-meta">
                                            <span class="chat-user-name">
                                                {{ $post->user_name }}
                                                @if($isTeacherPost)
                                                    <span class="badge bg-primary text-white text-uppercase ms-1 px-1.5 py-0.5" style="font-size: 0.55rem; font-weight: 700;">Instructor</span>
                                                @endif
                                            </span>
                                            <span class="chat-time">{{ date('h:i A', strtotime($post->created_at)) }}</span>
                                        </div>
                                        <p class="chat-content">{{ $post->content }}</p>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@if($isStaff)
    <!-- Modal for Adding a Lesson -->
    <div class="modal fade" id="createLessonModal" tabindex="-1" aria-labelledby="createLessonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow-lg bg-glass">
                <form action="{{ route('school.lms.lessons.store', $course->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom-0 p-4 pb-0">
                        <h5 class="modal-title fw-bold text-dark" id="createLessonModalLabel">Add Lesson to Curriculum</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="lesson_title" class="form-label text-dark fw-semibold">Lesson Title</label>
                            <input type="text" class="form-control rounded-3" id="lesson_title" name="title" required placeholder="e.g. Chapter 1: Introduction to Algebra">
                        </div>
                        <div class="mb-3">
                            <label for="lesson_content" class="form-label text-dark fw-semibold">Lesson Content</label>
                            <textarea class="form-control rounded-3" id="lesson_content" name="content" rows="6" required placeholder="Write the lesson syllabus description and notes..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="lesson_files" class="form-label text-dark fw-semibold">Lesson Attachments (Pictures, Word Docs, PDFs)</label>
                            <input type="file" class="form-control rounded-3" id="lesson_files" name="files[]" multiple accept="image/*,.pdf,.doc,.docx">
                            <small class="text-muted">You can select multiple files at once. Max 10MB per file.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4">Save Lesson</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Creating a Quiz -->
    <div class="modal fade" id="createQuizModal" tabindex="-1" aria-labelledby="createQuizModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg bg-glass">
                <form action="{{ route('school.lms.quizzes.store', $course->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 p-4 pb-0">
                        <h5 class="modal-title fw-bold text-dark" id="createQuizModalLabel">Create Assessment Quiz</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="quiz_title" class="form-label text-dark fw-semibold">Quiz Title</label>
                            <input type="text" class="form-control rounded-3" id="quiz_title" name="title" required placeholder="e.g. Term 1 Mid-Term Assessment">
                        </div>
                        <div class="mb-3">
                            <label for="passing_percentage" class="form-label text-dark fw-semibold">Passing Percentage (%)</label>
                            <input type="number" class="form-control rounded-3" id="passing_percentage" name="passing_percentage" required min="0" max="100" value="50">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning text-dark rounded-3 px-4">Create Quiz</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modals for Adding Questions to each Quiz -->
    @foreach($quizzes as $quiz)
        <div class="modal fade" id="addQuestionModal{{ $quiz->id }}" tabindex="-1" aria-labelledby="addQuestionModalLabel{{ $quiz->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg bg-glass">
                    <form action="{{ route('school.lms.quizzes.questions.store', $quiz->id) }}" method="POST">
                        @csrf
                        <div class="modal-header border-bottom-0 p-4 pb-0">
                            <h5 class="modal-title fw-bold text-dark" id="addQuestionModalLabel{{ $quiz->id }}">Add Multiple-Choice Question</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label text-dark fw-semibold">Question Text</label>
                                <input type="text" class="form-control rounded-3" name="question_text" required placeholder="e.g. What is the value of Pi?">
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label text-dark fw-semibold mb-0">Question Options</label>
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-3 px-2 py-1" style="font-size: 0.75rem;" onclick="addOption({{ $quiz->id }})">
                                        <i class="bi bi-plus-lg me-1"></i> Add Option
                                    </button>
                                </div>
                                <div id="optionsContainer{{ $quiz->id }}">
                                    <div class="input-group mb-2 option-input-wrapper">
                                        <input type="text" class="form-control rounded-start-3 option-input" name="options[]" required placeholder="Option 1">
                                    </div>
                                    <div class="input-group mb-2 option-input-wrapper">
                                        <input type="text" class="form-control rounded-start-3 option-input" name="options[]" required placeholder="Option 2">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-dark fw-semibold">Correct Answer (Must match correct option text exactly)</label>
                                <input type="text" class="form-control rounded-3" name="correct_answer" required placeholder="e.g. 3.14">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-dark fw-semibold">Award Points</label>
                                <input type="number" class="form-control rounded-3" name="points" required min="1" value="5">
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 p-4 pt-0">
                            <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-3 px-4">Save Question</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection

@section('scripts')
<script>
function addOption(quizId) {
    const container = document.getElementById('optionsContainer' + quizId);
    if (!container) return;
    const count = container.getElementsByClassName('option-input').length;
    
    const div = document.createElement('div');
    div.className = 'input-group mb-2 option-input-wrapper';
    div.innerHTML = `
        <input type="text" class="form-control rounded-start-3 option-input" name="options[]" required placeholder="Option ${count + 1}">
        <button type="button" class="btn btn-outline-danger rounded-end-3" onclick="this.closest('.option-input-wrapper').remove()">
            <i class="bi bi-trash"></i>
        </button>
    `;
    container.appendChild(div);
}
</script>
@endsection
