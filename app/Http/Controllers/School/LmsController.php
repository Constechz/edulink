<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\LmsCourse;
use App\Models\LmsLesson;
use App\Models\LmsProgress;
use App\Models\LmsQuiz;
use App\Models\LmsQuizQuestion;
use App\Models\LmsQuizAttempt;
use App\Models\LmsForum;
use App\Models\LmsForumPost;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LmsController extends Controller
{
    private function getStudent(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        $student = Student::where('school_id', $schoolId)
            ->where('student_id_number', $user->employee_id)
            ->first();

        if (!$student) {
            $student = Student::where('school_id', $schoolId)
                ->where(DB::raw("CONCAT(first_name, ' ', last_name)"), $user->name)
                ->first();
        }

        if (!$student) {
            $student = Student::where('school_id', $schoolId)->first();
        }

        return $student;
    }

    private function authorizeLmsManage(Request $request)
    {
        $user = $request->user();
        if (!$user || ($user->role && in_array($user->role->slug, ['student', 'parent']))) {
            abort(403, 'Unauthorized LMS management access.');
        }
    }

    public function coursesIndex(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $user = $request->user();
        
        $isStaff = $user->role && !in_array($user->role->slug, ['student', 'parent']);
        
        $query = LmsCourse::where('school_id', $schoolId)->with(['subject', 'teacher']);
        if (!$isStaff) {
            $query->where('is_active', true);
        }
        $courses = $query->get();

        $student = $this->getStudent($request);

        // Pre-calculate progress
        foreach ($courses as $course) {
            $lessonIds = $course->lessons()->pluck('id');
            $completedCount = 0;
            if ($student && $lessonIds->isNotEmpty()) {
                $completedCount = LmsProgress::where('student_id', $student->id)
                    ->whereIn('lesson_id', $lessonIds)
                    ->count();
            }
            $totalCount = $lessonIds->count();
            $course->progress_percent = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;
        }

        $subjects = [];
        $teachers = [];
        if ($isStaff) {
            $subjects = \App\Models\Subject::where('school_id', $schoolId)->get();
            $teachers = \App\Models\User::where('school_id', $schoolId)
                ->whereHas('role', function($q) {
                    $q->whereNotIn('slug', ['student', 'parent']);
                })
                ->get();
        }

        return view('school.lms.courses', compact('courses', 'subjects', 'teachers', 'isStaff'));
    }

    public function courseStore(Request $request)
    {
        $this->authorizeLmsManage($request);
        $schoolId = $request->user()->school_id;

        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048'
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('lms/thumbnails', 'public');
        }

        LmsCourse::create([
            'school_id' => $schoolId,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'title' => $request->title,
            'description' => $request->description,
            'thumbnail' => $thumbnailPath,
            'is_active' => true
        ]);

        return redirect()->back()->with('success', 'Course created successfully.');
    }

    public function courseShow(Request $request, LmsCourse $course)
    {
        $schoolId = $request->user()->school_id;
        if ($course->school_id !== $schoolId) {
            abort(403, 'Unauthorized LMS Course view.');
        }

        $course->load(['lessons.resources', 'teacher', 'subject']);
        $student = $this->getStudent($request);

        $completedLessonIds = [];
        if ($student) {
            $completedLessonIds = LmsProgress::where('student_id', $student->id)
                ->whereIn('lesson_id', $course->lessons->pluck('id'))
                ->pluck('lesson_id')
                ->toArray();
        }

        // Fetch Quizzes and Forums
        $quizzes = LmsQuiz::where('course_id', $course->id)->get();
        $forums = LmsForum::where('course_id', $course->id)->get();

        $user = $request->user();
        $isStaff = $user->role && !in_array($user->role->slug, ['student', 'parent']);

        return view('school.lms.course_show', compact('course', 'completedLessonIds', 'quizzes', 'forums', 'isStaff'));
    }

    public function lessonStore(Request $request, LmsCourse $course)
    {
        $this->authorizeLmsManage($request);
        if ($course->school_id !== $request->user()->school_id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:jpeg,png,jpg,pdf,doc,docx|max:10240'
        ]);

        $displayOrder = LmsLesson::where('course_id', $course->id)->count() + 1;

        $lesson = LmsLesson::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'content' => $request->content,
            'display_order' => $displayOrder
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('lms/lessons', 'public');
                $ext = $file->getClientOriginalExtension();
                
                \App\Models\LmsResource::create([
                    'lesson_id' => $lesson->id,
                    'title' => $file->getClientOriginalName(),
                    'resource_type' => in_array(strtolower($ext), ['png', 'jpg', 'jpeg']) ? 'image' : strtolower($ext),
                    'file_path' => $path,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Lesson added successfully.');
    }

    public function quizStore(Request $request, LmsCourse $course)
    {
        $this->authorizeLmsManage($request);
        if ($course->school_id !== $request->user()->school_id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'passing_percentage' => 'required|integer|min:0|max:100'
        ]);

        LmsQuiz::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'passing_percentage' => $request->passing_percentage
        ]);

        return redirect()->back()->with('success', 'Quiz added successfully.');
    }

    public function questionStore(Request $request, LmsQuiz $quiz)
    {
        $this->authorizeLmsManage($request);
        $course = $quiz->course;
        if (!$course || $course->school_id !== $request->user()->school_id) {
            abort(403);
        }

        $request->validate([
            'question_text' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:1'
        ]);

        LmsQuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => $request->question_text,
            'question_type' => 'single_choice',
            'options_json' => $request->options,
            'correct_answer' => $request->correct_answer,
            'points' => $request->points
        ]);

        return redirect()->back()->with('success', 'Question added to quiz successfully.');
    }

    public function lessonShow(Request $request, LmsLesson $lesson)
    {
        $course = $lesson->course;
        $schoolId = $request->user()->school_id;
        if ($course->school_id !== $schoolId) {
            abort(403, 'Unauthorized LMS Lesson view.');
        }

        $lesson->load(['resources']);
        $siblings = LmsLesson::where('course_id', $course->id)->orderBy('display_order')->get();
        
        $student = $this->getStudent($request);
        $isCompleted = false;
        if ($student) {
            $isCompleted = LmsProgress::where('student_id', $student->id)
                ->where('lesson_id', $lesson->id)
                ->exists();
        }

        return view('school.lms.lesson', compact('lesson', 'course', 'siblings', 'isCompleted'));
    }

    public function lessonComplete(Request $request, LmsLesson $lesson)
    {
        $student = $this->getStudent($request);
        if (!$student) {
            return redirect()->back()->withErrors(['error' => 'No student profile associated.']);
        }

        LmsProgress::updateOrCreate([
            'student_id' => $student->id,
            'lesson_id' => $lesson->id
        ], [
            'completed_at' => now()
        ]);

        return redirect()->back()->with('success', 'Lesson marked as completed.');
    }

    public function quizShow(Request $request, LmsQuiz $quiz)
    {
        $course = $quiz->course;
        if ($course->school_id !== $request->user()->school_id) {
            abort(403, 'Unauthorized Quiz view.');
        }

        $questions = LmsQuizQuestion::where('quiz_id', $quiz->id)->get();
        return view('school.lms.quiz', compact('quiz', 'course', 'questions'));
    }

    public function quizSubmit(Request $request, LmsQuiz $quiz)
    {
        $student = $this->getStudent($request);
        if (!$student) {
            return redirect()->back()->withErrors(['error' => 'No student profile associated.']);
        }

        $questions = LmsQuizQuestion::where('quiz_id', $quiz->id)->get();
        $totalPoints = $questions->sum('points');
        $earnedPoints = 0;

        $answers = $request->input('answers', []);

        foreach ($questions as $question) {
            $submitted = $answers[$question->id] ?? '';
            if (trim(strtolower($submitted)) === trim(strtolower($question->correct_answer))) {
                $earnedPoints += $question->points;
            }
        }

        $percentage = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;
        $isPassed = $percentage >= $quiz->passing_percentage;

        LmsQuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'score' => $percentage,
            'is_passed' => $isPassed,
            'attempted_at' => now(),
        ]);

        return view('school.lms.quiz_result', compact('quiz', 'percentage', 'isPassed', 'earnedPoints', 'totalPoints'));
    }

    public function forumPostStore(Request $request, LmsForum $forum)
    {
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:lms_forum_posts,id'
        ]);

        LmsForumPost::create([
            'forum_id' => $forum->id,
            'user_id' => $request->user()->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content
        ]);

        return redirect()->back()->with('success', 'Forum post submitted.');
    }
}
