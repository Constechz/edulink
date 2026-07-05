<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Services\AiAnalyticsService;
use App\Models\AiFlag;
use App\Models\AiRecommendation;
use Illuminate\Http\Request;

class AiController extends Controller
{
    protected $aiService;

    public function __construct(AiAnalyticsService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function dashboard(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $flags = AiFlag::where('school_id', $schoolId)
            ->where('is_resolved', false)
            ->with(['student', 'flagType'])
            ->get();

        $recommendations = AiRecommendation::where('school_id', $schoolId)
            ->where('status', 'pending')
            ->with('student')
            ->get();

        return view('school.operations.ai_dashboard', compact('flags', 'recommendations'));
    }

    public function runAnalytics(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $this->aiService->flagAtRiskStudents($schoolId);
        $this->aiService->predictFeeDefaultRisk($schoolId);

        return redirect()->back()->with('success', 'AI analytics executed successfully. Risk flags and recommendations updated.');
    }

    public function suggestComment(Request $request)
    {
        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $comment = $this->aiService->suggestReportCardComment($request->score);

        return response()->json([
            'success' => true,
            'comment' => $comment,
        ]);
    }
}
